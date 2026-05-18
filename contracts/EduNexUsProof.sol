// SPDX-License-Identifier: MIT
pragma solidity ^0.8.28;

contract EduNexUsProof {
    struct ClaimProof {
        string referenceCode;
        uint256 amount;
        uint256 merchantId;
        uint256 recordedAt;
        address recordedBy;
    }

    mapping(string => ClaimProof) private claimProofs;

    event ClaimProofRecorded(
        string referenceCode,
        uint256 amount,
        uint256 merchantId,
        uint256 recordedAt,
        address recordedBy
    );

    function recordClaimProof(
        string memory referenceCode,
        uint256 amount,
        uint256 merchantId
    ) public {
        require(bytes(referenceCode).length > 0, "Reference code required");
        require(bytes(claimProofs[referenceCode].referenceCode).length == 0, "Proof already recorded");

        claimProofs[referenceCode] = ClaimProof({
            referenceCode: referenceCode,
            amount: amount,
            merchantId: merchantId,
            recordedAt: block.timestamp,
            recordedBy: msg.sender
        });

        emit ClaimProofRecorded(
            referenceCode,
            amount,
            merchantId,
            block.timestamp,
            msg.sender
        );
    }

    function getClaimProof(string memory referenceCode)
        public
        view
        returns (ClaimProof memory)
    {
        return claimProofs[referenceCode];
    }
}