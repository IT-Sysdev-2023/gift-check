<template>
    <a-modal :footer="null">
        <template #title>
            <p class="text-center">Approved Production Request Details</p>
        </template>
        <a-card>
            <!-- {{data.items.data.}} -->
            <a-row :gutter="[16, 16]">
                <a-col :span="16">
                    <a-descriptions size="small" layout="vertical" bordered>
                        <a-descriptions-item label="PR No">{{ data.approved.pe_num }}</a-descriptions-item>
                        <a-descriptions-item label="Date Requested">{{ data.approved.pe_date_request
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Time Requested:">YES</a-descriptions-item>
                        <a-descriptions-item label="Requested Prepared By:">{{ data.approved.rname }}, {{
                            data.approved.rsname
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Remarks" :span="2">
                            <a-badge status="processing" :text="data.approved.pe_remarks" />
                        </a-descriptions-item>
                        <a-descriptions-item label="Date Approved">{{ data.approved.ape_approved_at
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Approved by">{{ data.approved.ape_approved_by
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Checked by">{{ data.approved.ape_checked_by }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Prepared By">{{ data.approved.apname }}, {{ data.approved.apname
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Approved Remarks" :span="2">
                            <a-badge status="processing" :text="data.approved.ape_remarks" />
                        </a-descriptions-item>
                    </a-descriptions>
                </a-col>
                <a-col :span="8">
                    <a-button block class="mb-3" @click="barcode">
                        <ArrowsAltOutlined /> Barcode Generated Details
                    </a-button>
                    <a-button block class="mb-3">
                        <ArrowsAltOutlined /> Requisiton Created Details
                    </a-button>
                    <a-button block class="mb-3" type="primary">
                        <PrinterOutlined /> Reprint this Request Details
                    </a-button>
                </a-col>
            </a-row>
            <a-divider><small>Barcode Tables</small></a-divider>
            <a-table bordered :data-source="data.items.data" :columns="data.column" :pagination="false" size="small">
            </a-table>

            <a-descriptions class="text-center mt-2" size="small" layout="horizontal" bordered>
                <a-descriptions-item style="width: 50%; font-weight: 700;" label="Total">{{ data.items.total
                    }}</a-descriptions-item>
            </a-descriptions>

            <a-modal v-model:open="bopen" title="Generated Barcode" :footer="null" :width="1000">
                <a-card>
                    <a-tabs type="card" centered v-model:activeKey="activeKey" @change="handlebarcode">
                        <a-tab-pane v-for="(bar, key) in barcodeDetails.record" :key="bar.denom_id">
                            <template #tab>
                                <span>
                                    <apple-outlined />
                                    {{ key }}
                                </span>
                            </template>
                        </a-tab-pane>
                    </a-tabs>
                </a-card>
            </a-modal>

        </a-card>
    </a-modal>
</template>

<script setup>
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps({
    data: Array,
    id: Number
});

const bopen = ref(false);
const activeKey = ref('1');
const barcodeDetails = ref([]);

const barcode = async (id) => {
    try {

        const { data } = await axios.get(route('custodian.production.barcode.details', props.id));
        bopen.value = true;
        barcodeDetails.value = data;

    } catch {

    }
}
const handlebarcode = (key) => {
    alert(key)
}
</script>
