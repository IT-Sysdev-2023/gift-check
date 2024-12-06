<template>
    <a-modal
        :open="open"
        :title="title"
        width="1000px"
        :footer="null"
        maskClosable
        @cancel="handleClose"
    >
        <a-tabs v-model:activeKey="activeKey" @change="handleTab">
            <a-tab-pane key="all" tab="All" force-render></a-tab-pane>
            <a-tab-pane
                v-for="denom of denoms"
                :key="denom.denomination"
                :tab="denom.denomination_format"
            ></a-tab-pane>
        </a-tabs>
        <a-table
            bordered
            :columns="columns"
            :data-source="data.data"
            :pagination="false"
        >
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
        <pagination-axios
            class="mt-5"
            :datarecords="data"
            @on-pagination="onChangePagination"
        />
    </a-modal>
</template>

<script lang="ts" setup>
import { StoreDenomination } from "@/types/treasury";
import { ref } from "vue";

const activeKey = ref("all");
defineProps<{
    title: string;
    denoms: StoreDenomination | null;
    open: boolean;
    data: {
        data: any[];
    };
    columns: {
        title: string;
        dataIndex: string;
    }[];
}>();

const emit = defineEmits<{
    (e: "update:open", value: boolean): void;
    (e: "handlePagination", link): void;
    (e: "handleTabChange", value): void;
}>();

const handleClose = () => {
    emit("update:open", false);
};
const handleTab = (val) => {
    emit("handleTabChange", val);
};
const getValue = (record, index) => {
    return index.reduce((acc, index) => acc[index], record);
};
const onChangePagination = async (link) => {
    emit("handlePagination", link);
};
</script>
