<template>
    <a-card>
        <div class="flex justify-end">
            <a-button @click="() => $inertia.visit(route('finance.dashboard'))" class="mb-2">
                <RollbackOutlined />
                Back to Dashboard
            </a-button>
        </div>
        <a-table :dataSource="data" :columns="columns" bordered size="small">
            <template v-slot:bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'View'">
                    <a-button type="primary" @click="approvedSpexGcDetails(record)">
                        <PicLeftOutlined />View
                    </a-button>
                </template>
            </template>
        </a-table>
    </a-card>
    <a-drawer :placement="placement" :height="520" :closable="true" :open="open" @close="onClose">
        <a-tabs v-model:activeKey="activeKey">
            <a-tab-pane key="1" :tab="'Special External GC Request # ' + selectedData.spexgc_num">
                <a-row :gutter="[16, 16]">
                    <a-col :span="6">
                        <a-card>
                            <a-form-item label="Date Requested">
                                <a-input v-model:value="selectedData.dateRequested" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Document">
                                <a-image style="height: 150px;" :src="'/storage/' + selectedData.reqap_doc"></a-image>
                            </a-form-item>
                        </a-card>
                    </a-col>
                    <a-col :span="6">
                        <a-card>
                            <a-form-item label="Date Validity">
                                <a-input v-model:value="selectedData.dateNeeded" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Remarks">
                                <a-input v-model:value="selectedData.spexgc_remarks" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="AR #">
                                <a-input v-model:value="selectedData.spexgc_payment_arnum" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Payment Type">
                                <a-input v-model:value="paymentType" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Amount">
                                <a-input v-model:value="selectedData.spexgc_payment" readonly></a-input>
                            </a-form-item>
                        </a-card>
                    </a-col>
                    <a-col :span="6">
                        <a-card>
                            <a-form-item label="Date Approved">
                                <a-input v-model:value="selectedData.dateApproved" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Checked By">
                                <a-input v-model:value="selectedData.reqap_checkedby" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Prepared By">
                                <a-input v-model:value="selectedData.preparedBy" readonly></a-input>
                            </a-form-item>
                        </a-card>
                    </a-col>
                    <a-col :span="6">
                        <a-card>
                            <a-form-item label="Remarks">
                                <a-input v-model:value="selectedData.reqap_remarks" readonly></a-input>
                            </a-form-item>
                            <a-form-item label="Approved By">
                                <a-input v-model:value="selectedData.reqap_approvedby" readonly></a-input>
                            </a-form-item>
                        </a-card>
                    </a-col>
                </a-row>
            </a-tab-pane>
            <a-tab-pane  key="2" tab="Barcodes" force-render>
                <a-table :pagination="false" bordered :dataSource="barcode" :columns="barcodecolumns" />
            </a-tab-pane>
        </a-tabs>
    </a-drawer>

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";

export default {
    layout: AuthenticatedLayout,

    props: {
        data: Object,
        columns: Object,
    },
    data() {
        return {
            details: '',
            selectedData: null,
            open: false,
            placement: 'bottom',
            activeKey: '1',
            barcode: null,
            barcodecolumns: [
                {
                    title: 'Barcode',
                    dataIndex: 'spexgcemp_barcode',
                },
                {
                    title: 'Denomination',
                    dataIndex: 'spexgcemp_denom',

                },
                {
                    title: 'Fullname',
                    dataIndex: 'fullname',
                },
            ],
        };
    },
    computed: {
        paymentType() {
            return this.paymentTypeText();
        }
    },
    methods: {
        approvedSpexGcDetails(data) {
            axios.get(route('finance.approvedGc.selected.approved'), {
                params: {
                    id: data.spexgc_id
                }
            }).then((response) => {
                this.open = true;
                this.selectedData = response.data.data[0];
                this.barcode = response.data.barcodes;
            });
        },
        onClose() {
            this.open = false;
        },
        paymentTypeText() {
            const paymentTypes = {
                '1': 'Cash',
                '2': 'Check',
                '3': 'JV',
                '4': 'AR',
                '5': 'On Account'
            };
            return this.selectedData ? paymentTypes[this.selectedData.spexgc_paymentype] || '' : '';
        }

    },
};
</script>
