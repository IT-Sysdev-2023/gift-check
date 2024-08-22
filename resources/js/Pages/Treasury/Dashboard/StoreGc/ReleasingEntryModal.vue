<template>
    <a-modal
        :open="open"
        width="1050px"
        centered
        @cancel="handleClose"
        title="Releasing Entry"
    >
        <a-row :gutter="[16, 0]" class="mt-8">
            <a-col :span="10">
                <a-card>
                    <a-form
                        layout="horizontal"
                        style="max-width: 600px; padding-top: 10px"
                    >
                        <a-form-item label="GC Releasing No.:">
                            <a-input :value="data.rel_num" readonly />
                        </a-form-item>
                        <a-form-item label="Date Released:">
                            <a-input :value="today" readonly />
                        </a-form-item>
                        <a-form-item label="Upload Document:">
                            <a-input :value="data.rel_num" />
                        </a-form-item>
                        <a-form-item label="Remarks:">
                            <a-textarea :value="data.rel_num" />
                        </a-form-item>
                        <a-form-item label="Checked By:">
                            <a-input :value="data.rel_num" readonly />
                        </a-form-item>
                        <a-form-item label="Released By:">
                            <a-textarea :value="data.rel_num" readonly />
                        </a-form-item>

                        <a-form-item label="Received By:">
                            <a-input :value="data.rel_num" readonly />
                        </a-form-item>
                        <a-form-item label="Payment Type:">
                            <a-input :value="data.rel_num" readonly />
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
            <a-col :span="14">
                <a-card>
                    <a-descriptions title="More Details">
                        <a-descriptions-item label="Store">{{
                            data.details.store.store_name
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Date Requested"
                            >date</a-descriptions-item
                        >
                        <a-descriptions-item label="Date Needed"
                            >Hangzhou, Zhejiang</a-descriptions-item
                        >
                        <a-descriptions-item label="Remarks"
                            >empty</a-descriptions-item
                        >
                        <a-descriptions-item label="Requested By">
                            No. 18,
                        </a-descriptions-item>
                        <a-descriptions-item label="GC Request No">
                           {{  data.details.sgc_num }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Time Requested">
                            No. 18
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-table
                        bordered
                        :pagination="false"
                        size="small"
                        :columns="[
                            {
                                title: 'Denomination',
                                dataIndex: 'denomination',
                            },
                            {
                                title: 'Quantity',
                                dataIndex: 'sri_items_quantity',
                                width: '100px',
                            },
                            {
                                title: 'Total',
                                dataIndex: 'total',
                                width: '100px',
                            },
                        ]"
                    >
                    </a-table>
                </a-card>
            </a-col>
        </a-row>
    </a-modal>
</template>

<script lang="ts" setup>
import dayjs from "dayjs";
defineProps<{ open: boolean; data: { rel_num: number; details: any } }>();
const emit = defineEmits<{
    (e: "update:open", value: boolean): void;
}>();

const handleClose = () => {
    emit("update:open", false);
};

const today = dayjs();
</script>
