<template>
    <a-descriptions title="User Info" layout="vertical" bordered size="small">
        <a-descriptions-item label="BR No.">{{
            data.br_no
        }}</a-descriptions-item>

        <a-descriptions-item label="Date Requested">{{
            data.br_requested_at
        }}</a-descriptions-item>
        <a-descriptions-item label="Time Requested">{{
            data.br_requested_at_time
        }}</a-descriptions-item>
        <a-descriptions-item label="Date Needed">{{
            data.br_requested_needed
        }}</a-descriptions-item>
        <a-descriptions-item label="Budget Requested" :span="2">{{
            data.br_request
        }}</a-descriptions-item>
        <a-descriptions-item label="Request Document">{{
            data.br_file_docno
        }}</a-descriptions-item>
        <a-descriptions-item label="Request Remark">{{
            data.br_remarks
        }}</a-descriptions-item>
        <a-descriptions-item label="Prepared By">{{
            data.prepared_by.full_name
        }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions layout="vertical" bordered style="margin-top: 20px">
        <a-descriptions-item label="Date Approved">{{
            data.abr.approved_at
        }}</a-descriptions-item>

        <a-descriptions-item label="Approved Document">{{
            data.abr.file_doc_no
        }}</a-descriptions-item>
        <a-descriptions-item label="Approved Remarks"
            ><a-tag color="cyan">{{
                data.abr.budget_remark
            }}</a-tag></a-descriptions-item
        >
        <a-descriptions-item label="Approved By" :span="2">{{
            data.abr.approved_by
        }}</a-descriptions-item>
        <a-descriptions-item label="Checked By">{{
            data.abr.checked_by
        }}</a-descriptions-item>
        <a-descriptions-item label="Prepared By">{{
            data.abr.user_prepared_by.full_name
        }}</a-descriptions-item>
    </a-descriptions>
</template>

<script>
export default {
    props: {
        data: Object,
    },
};
</script>
