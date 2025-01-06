<template>
    <a-modal style="width: auto; top: 50px;" title="Details"  @close="close">
        <a-descriptions size="small" layout="vertical" bordered>
            <a-descriptions-item label="Pr No">{{ record.data.pe_num }}</a-descriptions-item>
            <a-descriptions-item label="Date Requested">{{record.data.req_date}}</a-descriptions-item>
            <a-descriptions-item label="Request Prepared by">{{ record.data.prepby }}</a-descriptions-item>
            <a-descriptions-item label="Cancelled by">{{ record.data.canby }}</a-descriptions-item>
            <a-descriptions-item label="Date Cancelled" :span="2">{{record.data.can_at}}</a-descriptions-item>
            <a-descriptions-item label="Requested Remarks" :span="3">
                <a-badge status="processing" :text="record.data.remarks" />
            </a-descriptions-item>
        </a-descriptions>

        <a-table size="small" bordered class="mt-5" :columns="[
            {
                title: 'Denomination',
                dataIndex: 'Denomination',
            },
            {
                title: 'Barcode Start',
                dataIndex: 'Denomination',
            },
            {
                title: 'Barcode End',
                dataIndex: 'Denomination',
            },
        ]" />
    </a-modal>

</template>
<script setup>

defineProps({
    record: Object,
})

const emit = defineEmits('close-drawer');

const close = () => {
    emit('close-drawer');
}

</script>
