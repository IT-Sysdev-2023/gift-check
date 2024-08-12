<template>

    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="FAD Receiving No:">{{ record.rec_no }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Transaction Date:">{{ record.trans_date }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Reference No:">{{ record.ref_no }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Purchase Order No:">{{ record.po_no }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Purchase Date:">{{ record.pur_date }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Reference PO No:">{{ record.ref_po_no }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Payment Terms:">{{ record.pay_terms }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Location Code:">{{ record.loc_code }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Department Code:">{{ record.dep_code }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Supplier Name:">{{ record.sup_name }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Mode of Payment:">{{ record.mop }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Remarks:">{{ record.remarks }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Checked By:">{{ record.check_by }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item class="text-end" label="Prepared By:">{{ record.prep_by }}</a-descriptions-item>
    </a-descriptions>

</template>
<script>
export default {
    props: {
        record: Object
    }
}
</script>
