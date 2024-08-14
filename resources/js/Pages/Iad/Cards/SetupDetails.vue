<template>

    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="FAD Receiving No:">{{ record.rec_no }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Transaction Date:">{{ record.trans_date }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Reference No:">{{ record.ref_no }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Purchase Order No:">{{ record.po_no }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Purchase Date:">{{ record.pur_date }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Reference PO No:">{{ record.ref_po_no }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Payment Terms:">{{ record.pay_terms }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Location Code:">{{ record.loc_code }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Department Code:">{{ record.dep_code }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Supplier Name:">{{ record.sup_name }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Mode of Payment:">{{ record.mop }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Remarks:">{{ record.remarks }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Checked By:">{{ record.check_by }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item  style="width: 50%;" label="Prepared By:">{{ record.prep_by }}</a-descriptions-item>
    </a-descriptions>

</template>
<script>
export default {
    props: {
        record: Object
    }
}
</script>
