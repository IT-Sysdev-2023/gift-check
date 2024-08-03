<template>
    <a-card>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="FAD Receiving No:">{{ record.recno }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Transaction Date:">{{ record.transdate }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Reference No:">{{ record.refno }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Purchase Order No:">{{ record.po }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Purchase Date:">{{ record.purdate }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Reference PO No:">{{ record.rpn }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Payment Terms:">{{ record.payterms }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Location Code:">{{ record.loccode }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Department Code:">{{ record.depcode }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Supplier Name:">{{ record.supname }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Mode of Payment:">{{ record.mop }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Remarks:">{{ record.remarks }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Checked By:">{{ record.checkby }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item class="text-end" label="Prepared By:">{{ record.preby }}</a-descriptions-item>
        </a-descriptions>
    </a-card>
</template>
<script>
export default {
    props: {
        record: Object
    }
}
</script>
