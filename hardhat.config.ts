import { defineConfig } from "hardhat/config";
import "@nomicfoundation/hardhat-ethers";
import "dotenv/config";
import process from "process";

export default defineConfig({
  solidity: {
    version: "0.8.28",
  },
  networks: {
    morphHolesky: {
      type: "http",
      url: process.env.MORPH_RPC_URL || "",
      accounts: process.env.MORPH_PRIVATE_KEY
        ? [process.env.MORPH_PRIVATE_KEY]
        : [],
    },
  },
});