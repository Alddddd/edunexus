import "dotenv/config";
import { ethers } from "ethers";
import fs from "fs";

async function main() {
    const referenceCode = process.argv[2];
    const amount = process.argv[3];
    const merchantId = process.argv[4];

    if (!referenceCode || !amount || !merchantId) {
        throw new Error("Missing required arguments.");
    }

    const rpcUrl = process.env.MORPH_RPC_URL;
    let privateKey = process.env.MORPH_PRIVATE_KEY;
    const contractAddress = process.env.MORPH_CONTRACT_ADDRESS;

    if (!rpcUrl || !privateKey || !contractAddress) {
        throw new Error("Missing Morph blockchain environment configuration.");
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

    console.log(JSON.stringify({
        success: true,
        transaction_hash: receipt.hash
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