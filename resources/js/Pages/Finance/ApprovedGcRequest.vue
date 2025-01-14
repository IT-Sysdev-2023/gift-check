<template>
    <a-card>
        <div class="flex justify-end">
            <a-button @click="() => $inertia.visit(route('finance.dashboard'))" class="mb-2">
                <RollbackOutlined />
                Back to Dashboard
            </a-button>
        </div>
        <a-input-search allow-clear enter-button placeholder="Input search here..." v-model:value="approvedGcRequestSearch" style="width:25%; margin-left:75%"/>

        <a-table :dataSource="data.data" :columns="columns" bordered size="small" :pagination="false" style="margin-top: 10px">
            <template v-slot:bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'View'">
                    <a-button type="primary" @click="approvedSpexGcDetails(record)">
                        <PicLeftOutlined />View
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination :datarecords="data" class="mt-5" />
        <!-- {{ data }} -->
        <a-modal v-model:open="openModal" @ok="okay">
            <span style="color:red">
                {{ this.searchMessage }}
            </span>
        </a-modal>
    </a-card>
    <a-modal style="width: auto; top: 50px;" v-model:open="open" >
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
    </a-modal>

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";
import Pagination from '@/Components/Pagination.vue';
import {notification} from 'ant-design-vue';


export default {
  components: { Pagination },
    layout: AuthenticatedLayout,

    props: {
        data: Object,
        columns: Object,
    },
    data() {
        return {
            openModal: false,
            searchMessage: '',
            approvedGcRequestSearch: '',
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
    watch: {
        approvedGcRequestSearch(search){
           const searchValidation = /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
            if(searchValidation.test(search)){
                const openNotificationWithIcon = (type) =>{
                    notification[type]({
                        message: 'Invalid input',
                        description: 'Search contains invalid symbol or emojis',
                        placement: 'topRight'
                    });
                };
                openNotificationWithIcon('warning');
                return;
                }
            this.$inertia.get(route('finance.approvedGc.approved'),{
                search: search
            },{
                preserveState: true
            });

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
        },
        okay (){
            this.openModal =false;
        }

    },
}
</script>
