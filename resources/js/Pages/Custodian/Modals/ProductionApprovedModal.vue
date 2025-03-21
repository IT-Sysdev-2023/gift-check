<template>
    <a-modal :footer="null">
        <template #title>
            <p class="text-center">Approved Production Request Details</p>
        </template>
        <a-card>
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
                        <a-descriptions-item label="Approved by">{{ data.approved.apname
                            }},{{ data.approved.apsurname
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Checked by">{{ data.approved.cfname }}, {{ data.approved.csname
                            }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Prepared By">{{ data.approved.prepfname }}, {{ data.approved.prepsurname
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
                    <a-button block class="mb-3" @click="requistion">
                        <ArrowsAltOutlined /> Requisiton Created Details
                    </a-button>
                    <a-button block class="mb-3" type="primary" @click="
                        reprint(data.approved.pe_num)
                        ">
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

            <a-modal v-model:open="bopen" title="Generated Barcode" :footer="null" :width="1000" :after-close="close">
                <a-card>
                    <a-tabs type="card" v-model:activeKey="statuskey" @change="valStatus">
                        <a-tab-pane key="1">
                            <template #tab>
                                <span>
                                    <apple-outlined />
                                    GC for Validation
                                </span>
                            </template>
                            <a-tabs type="card" v-model:activeKey="activeKey" @change="handlebarcode">
                                <a-tab-pane v-for="(bar, key) in barcodeDetails.record" :key="bar.denom_id">
                                    <template #tab>
                                        <span>
                                            <apple-outlined />
                                            {{ key }}
                                        </span>
                                    </template>
                                    <a-table :loading="isloading" bordered size="small" :data-source="bardet" :columns="[{
                                        title: 'Barcode No',
                                        dataIndex: 'barcode_no',
                                    },
                                    {
                                        title: 'Denomination No',
                                        dataIndex: 'denomination',
                                    },
                                    ]">
                                    </a-table>
                                </a-tab-pane>
                            </a-tabs>
                            <div
                                v-if="activeKey === null && Object.keys(barcodeDetails.record).length !== 0 && statuskey === '1'">
                                <a-empty>
                                    <template #description>
                                        Please select Denomination above
                                        <ArrowUpOutlined />
                                    </template>
                                </a-empty>
                            </div>
                        </a-tab-pane>
                        <a-tab-pane key="2" force-render>
                            <template #tab>
                                <span>
                                    <apple-outlined />
                                    Validated GC
                                </span>
                            </template>
                            <a-tabs type="card" v-model:activeKey="activeKey" @change="handlebarcode">
                                <a-tab-pane v-for="(bar, key) in barcodeDetails.record" :key="bar.denom_id">
                                    <template #tab>
                                        <span>
                                            <apple-outlined />
                                            {{ key }}
                                        </span>
                                    </template>
                                    <a-table :loading="isloading" bordered size="small" :data-source="bardet" :columns="[{
                                        title: 'Barcode No',
                                        dataIndex: 'barcode_no',
                                    },
                                    {
                                        title: 'Denomination No',
                                        dataIndex: 'denomination',
                                    },
                                    ]">
                                    </a-table>
                                </a-tab-pane>
                            </a-tabs>
                        </a-tab-pane>
                    </a-tabs>
                    <div
                        v-if="activeKey === null && Object.keys(barcodeDetails.record).length !== 0 && statuskey === '2'">
                        <a-empty>
                            <template #description>
                                Please select Denomination above
                                <ArrowUpOutlined />
                            </template>
                        </a-empty>
                    </div>
                    <div v-if="Object.keys(barcodeDetails.record).length === 0 && statuskey !== null">
                        <a-empty>
                            <template #description>
                                Empty Denomination
                            </template>
                        </a-empty>
                    </div>
                </a-card>
            </a-modal>

            <a-modal v-model:open="ropen" :width="1000">
                <template #title>
                    <p class="text-center"> Requisition Details</p>
                </template>
                <a-card>
                    <a-descriptions size="small" layout="vertical" bordered>
                        <a-descriptions-item label="Request No">{{ requisitionDetails.requis_erno
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Date Requested">{{ requisitionDetails.requis_req
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Location">{{ requisitionDetails.requis_loc }}</a-descriptions-item>
                        <a-descriptions-item label="Department">{{ requisitionDetails.requis_dept
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Remarks" :span="3">{{ requisitionDetails.requis_rem
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Checked By">{{ requisitionDetails.requis_checked
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Approved By">{{ requisitionDetails.requis_approved
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Prepared By" :span="2">{{ requisitionDetails.firstname }}, {{
                            requisitionDetails.lastname }}</a-descriptions-item>

                        <a-descriptions-item label="Supplier Information" class="mt-4">
                            <span class="font-bold"> Company Name:</span> {{ requisitionDetails.gcs_companyname }}
                            <br />
                            <span class="font-bold"> Contact Person:</span> {{ requisitionDetails.gcs_contactperson }}
                            <br />
                            <span class="font-bold"> Contact Number:</span> {{ requisitionDetails.gcs_contactnumber }}
                            <br />
                            <span class="font-bold"> Company Address:</span> {{ requisitionDetails.gcs_address }}
                            <br />
                        </a-descriptions-item>
                    </a-descriptions>
                </a-card>
            </a-modal>

        </a-card>
    </a-modal>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { notification } from "ant-design-vue";

const props = defineProps({
    data: Object,
    id: Number
});

const bopen = ref(false);
const ropen = ref(false);
const activeKey = ref(null);
const statuskey = ref('1');
const barcodeDetails = ref([]);
const requisitionDetails = ref([]);
const bardet = ref([]);
const isloading = ref(false);

const barcode = async (id) => {
    try {
        const { data } = await axios.get(route('custodian.production.barcode.details', props.id), {
            params: {
                status: '',
            }
        });
        bopen.value = true;
        barcodeDetails.value = data;

    } catch (error) {
        console.error(error);
    }

}
const handlebarcode = async (key) => {

    let isKey = statuskey.value === '1' ? '' : '*';

    isloading.value = true;

    try {
        const { data } = await axios.get(route('custodian.production.barcode.every', props.data.items.data[0].pe_items_request_id), {
            params: {
                key,
                status: isKey,
            }
        });

        bardet.value = data.record;
        activeKey.value = key;
        isloading.value = false;

    } catch {
        isloading.value = false;
    }
}

const valStatus = async (key) => {

    let isKey = key === '1' ? '' : '*';

    try {

        const { data } = await axios.get(route('custodian.production.barcode.details', props.id), {
            params: {
                status: isKey,
            }
        });

        bopen.value = true;
        barcodeDetails.value = data;

    } catch (error) {
        console.error(error);
    }
}

const close = () => {
    // barcodeDetails.value = null;
    statuskey.value = '1';
    activeKey.value = null;
}

const requistion = async () => {
    try {
        const { data } = await axios.get(route('custodian.production.requisition', props.id));
        requisitionDetails.value = data;
        ropen.value = true;
    } catch {

    }
}

const reprint = (prNo) => {
    const url = route("treasury.production.request.reprint", {
        id: prNo,
    });

    axios
        .get(url, { responseType: "blob" })
        .then((response) => {
            const file = new Blob([response.data], {
                type: "application/pdf",
            });
            const fileURL = URL.createObjectURL(file);
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            if (error.response && error.response.status === 404) {
                notification.error({
                    message: "Pdf not Found",
                    description:
                        "Pdf Not available in Marketing.",
                });
            } else {
                console.error(error);
                alert("An error occurred while generating the PDF.");
            }
        });
}


</script>
