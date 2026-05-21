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

    const artifact = JSON.parse(
        fs.readFileSync(
            "./artifacts/contracts/EduNexUsSettlementToken.sol/EduNexUsSettlementToken.json",
            "utf8"
        )
    );

    const provider = new ethers.JsonRpcProvider(rpcUrl);
    const wallet = new ethers.Wallet(privateKey, provider);

    console.log("Deploying EduNexUs Settlement Token...");
    console.log("Deployer:", wallet.address);

    const factory = new ethers.ContractFactory(
        artifact.abi,
        artifact.bytecode,
        wallet
    );

    const token = await factory.deploy();
    await token.waitForDeployment();

    const tokenAddress = await token.getAddress();
    const symbol = await token.symbol();
    const decimals = Number(await token.decimals());
    const totalSupply = await token.totalSupply();

    console.log("Token address:", tokenAddress);
    console.log("Token name:", await token.name());
    console.log("Token symbol:", symbol);
    console.log("Token decimals:", decimals);
    console.log("Initial supply:", `${ethers.formatUnits(totalSupply, decimals)} ${symbol}`);
}

main().catch((error) => {
    console.error(error.message);
    process.exitCode = 1;
});
