import "dotenv/config";
import { ethers } from "ethers";
import fs from "fs";
import path from "path";

function requireEnv(name) {
    const value = process.env[name];

    if (!value || !value.trim()) {
        throw new Error(`${name} is missing in environment.`);
    }

    return value.trim();
}

function normalizePrivateKey(privateKey) {
    return privateKey.startsWith("0x") ? privateKey : `0x${privateKey}`;
}

async function main() {
    const referenceCode = process.argv[2];
    const amount = process.argv[3];
    const merchantId = process.argv[4];

    if (!referenceCode || !amount || !merchantId) {
        throw new Error("Missing required arguments.");
    }

    const rpcUrl = requireEnv("MORPH_RPC_URL");
    const privateKey = normalizePrivateKey(requireEnv("MORPH_PRIVATE_KEY"));
    const contractAddress = requireEnv("MORPH_CONTRACT_ADDRESS");

    if (!ethers.isAddress(contractAddress)) {
        throw new Error("MORPH_CONTRACT_ADDRESS must be a valid EVM address.");
    }

    try {
        new URL(rpcUrl);
    } catch (error) {
        throw new Error("MORPH_RPC_URL must be a valid URL.");
    }

    const artifactPath = path.join(
        process.cwd(),
        "artifacts/contracts/EduNexUsProof.sol/EduNexUsProof.json"
    );
    const artifact = JSON.parse(
        fs.readFileSync(artifactPath, "utf8")
    );

    const provider = new ethers.JsonRpcProvider(rpcUrl);
    const wallet = new ethers.Wallet(privateKey, provider);

    const contract = new ethers.Contract(
        contractAddress,
        artifact.abi,
        wallet
    );

    const amountInCents = Math.round(Number(amount) * 100);

    const tx = await contract.recordClaimProof(
        referenceCode,
        amountInCents,
        Number(merchantId)
    );

    const receipt = await tx.wait();
    const transactionHash = receipt.transactionHash || receipt.hash || tx.hash;

    console.log(JSON.stringify({
        success: receipt.status === 1,
        transaction_hash: transactionHash,
        transactionHash,
        hash: transactionHash,
        receipt_status: receipt.status === 1 ? "success" : "failed",
        block_number: receipt.blockNumber
    }));
}

main().catch((error) => {
    console.log(JSON.stringify({
        success: false,
        transaction_hash: null,
        error: error.message
    }));

    process.exitCode = 1;
});
