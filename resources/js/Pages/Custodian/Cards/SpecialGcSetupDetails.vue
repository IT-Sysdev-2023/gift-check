<template>
    <!-- {{record}} -->
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item style="width: 50%;" label="GC Request">{{record[0]?.spexgc_num}}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item style="width: 50%;" label="Date Requested">{{record[0]?.spexgc_datereq}}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item style="width: 50%;" label="Date Validity">{{record[0]?.spexgc_dateneed}}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" class="mt-3" layout="horizontal" bordered>
        <a-descriptions-item style="width: 50%;" label="Company Name">{{record[0]?.special_external_customer.spcus_companyname}}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small"  layout="horizontal" bordered>
        <a-descriptions-item style="width: 50%;" label="Account Name">{{record[0]?.special_external_customer.spcus_acctname}}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" class="mt-3" layout="horizontal" bordered>
        <a-descriptions-item style="width: 50%;" label="AR #">{{record[0]?.spexgc_payment_arnum}}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small"  layout="horizontal" bordered>
        <a-descriptions-item style="width: 50%;" label="Payment Type">{{record[0]?.spexgc_type}}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small"  layout="horizontal" bordered>
        <a-descriptions-item style="width: 50%;" label="Amount Paid">{{record[0]?.spexgc_paymentype}}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small"  layout="horizontal" bordered>
        <a-descriptions-item style="width: 50%;" label="Amount in words">{{record[0]?.spexgc_paymentype}}</a-descriptions-item>
    </a-descriptions>
</template>

<script>
export default {
    props: {
        record: Object
    }
}
</script>
