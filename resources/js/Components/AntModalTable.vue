<template>
    <a-modal
        :open="open"
        :title="title"
        width="1000px"
        :footer="null"
        maskClosable
        @cancel="handleClose"
    >
        <a-table bordered :columns="columns" :data-source="data.data" :pagination="false">
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'fullname'">
                    {{ record.user.full_name }}
                </template>
                <template v-if="column.key === 'gctype'">
                    {{ record.gc_type.gctype }}
                </template>
                <template v-if="column.key === 'productionrequest'">
                    {{ record.gc.production_request.pe_num }}
                </template>
                <template v-if="column.key === 'denom'">
                    {{ record.gc.denomination.denomination }}
                </template>
            </template>
        </a-table>
        <pagination class="mt-5" :datarecords="data" />
    </a-modal>
</template>

<script lang="ts" setup>
const props = defineProps<{
    title: string;
    open: boolean;
    data: {
        data: any[]
    };
    columns: {
        title: string;
        dataIndex: string;
    }[];
}>();
const emit = defineEmits<{
    (e: "update:open", value: boolean): void;
}>();

const handleClose = () => {
    emit("update:open", false);
};
const getValue = (record, index) => {
    return index.reduce((acc, index) => acc[index], record);
};
</script>
