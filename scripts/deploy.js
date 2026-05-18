import "dotenv/config";
import { ethers } from "ethers";
import fs from "fs";

async function main() {
    const rpcUrl = process.env.MORPH_RPC_URL;
    let privateKey = process.env.MORPH_PRIVATE_KEY;

    if (!rpcUrl) {
        throw new Error("MORPH_RPC_URL is missing in .env");
    }

    if (!privateKey) {
        throw new Error("MORPH_PRIVATE_KEY is missing in .env");
    }

    if (!privateKey.startsWith("0x")) {
        privateKey = "0x" + privateKey;
    }

    const artifact = JSON.parse(
        fs.readFileSync(
            "./artifacts/contracts/EduNexUsProof.sol/EduNexUsProof.json",
            "utf8"
        )
    );

    const provider = new ethers.JsonRpcProvider(rpcUrl);
    const wallet = new ethers.Wallet(privateKey, provider);

    console.log("Deploying from:", wallet.address);

    const factory = new ethers.ContractFactory(
        artifact.abi,
        artifact.bytecode,
        wallet
    );

    const contract = await factory.deploy();

    await contract.waitForDeployment();

    console.log("EduNexUsProof deployed to:");
    console.log(await contract.getAddress());
}

main().catch((error) => {
    console.error(error);
    process.exitCode = 1;
});