import "dotenv/config";
import { ethers } from "ethers";
import fs from "fs";

function requireEnv(name) {
    const value = process.env[name];

    if (!value) {
        throw new Error(`${name} is missing in .env`);
    }

    return value;
}

function normalizePrivateKey(privateKey) {
    return privateKey.startsWith("0x") ? privateKey : `0x${privateKey}`;
}

async function main() {
    const rpcUrl = requireEnv("MORPH_RPC_URL");
    const privateKey = normalizePrivateKey(requireEnv("MORPH_PRIVATE_KEY"));
    const tokenAddress = requireEnv("EDUX_TOKEN_ADDRESS");
    const recipientWallet = requireEnv("EDUX_SETTLEMENT_RECIPIENT_WALLET");
    const configuredDecimals = process.env.EDUX_TOKEN_DECIMALS;
    const transferAmount = process.env.EDUX_DEMO_TRANSFER_AMOUNT || "1";

    if (!ethers.isAddress(tokenAddress)) {
        throw new Error("EDUX_TOKEN_ADDRESS must be a valid EVM address.");
    }

    if (!ethers.isAddress(recipientWallet)) {
        throw new Error("EDUX_SETTLEMENT_RECIPIENT_WALLET must be a valid EVM address.");
    }

    const artifact = JSON.parse(
        fs.readFileSync(
            "./artifacts/contracts/EduNexUsSettlementToken.sol/EduNexUsSettlementToken.json",
            "utf8"
        )
    );

    const provider = new ethers.JsonRpcProvider(rpcUrl);
    const wallet = new ethers.Wallet(privateKey, provider);
    const token = new ethers.Contract(tokenAddress, artifact.abi, wallet);

    const symbol = await token.symbol();
    const decimals = configuredDecimals ? Number(configuredDecimals) : Number(await token.decimals());

    if (!Number.isInteger(decimals) || decimals < 0) {
        throw new Error("EDUX_TOKEN_DECIMALS must be a non-negative integer.");
    }

    const amount = ethers.parseUnits(transferAmount, decimals);

    const tx = await token.transfer(recipientWallet, amount);
    const receipt = await tx.wait();

    console.log(JSON.stringify({
        success: receipt.status === 1,
        transaction_hash: tx.hash,
        receipt_status: receipt.status === 1 ? "success" : "failed",
        from_address: wallet.address,
        to_address: recipientWallet,
        token_symbol: symbol,
        token_amount: transferAmount,
        token_contract: tokenAddress,
        block_number: receipt.blockNumber,
    }));
}

main().catch((error) => {
    console.log(JSON.stringify({
        success: false,
        transaction_hash: null,
        receipt_status: "failed",
        error: error.message,
    }));

    process.exitCode = 1;
});
