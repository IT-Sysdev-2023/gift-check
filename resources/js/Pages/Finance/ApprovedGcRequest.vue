<template>
    <a-table :dataSource="data" :columns="columns">
        <template v-slot:bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'View'">
                <a-button type="primary" @click="approvedSpexGcDetails(record)">
                    <PicLeftOutlined />View
                </a-button>
            </template>
        </template>
    </a-table>

    <a-modal v-model:open="open" width="95%" style="top: 65px;" title="Approved Special External Gc" @ok="handleOk">
        <a-row :gutter="[16, 16]">
            <a-col :span="6">
                <a-card>
                    <a-form-item label="Date Requested">
                        <a-input v-model:value="selectedGcData[0]['dateReq']"></a-input>
                    </a-form-item>
                    <a-form-item label="Requested By">
                        <a-textarea v-model:value="selectedGcData[0]['requestedBy']"></a-textarea>
                    </a-form-item>
                </a-card>
            </a-col>
            <a-col :span="6">
                <a-card>
                    <a-form-item label="Date Validity">
                        <a-input v-model:value="selectedGcData[0]['spexgc_dateneed']"></a-input>
                    </a-form-item>
                    <a-form-item label="Remarks">
                        <a-input v-model:value="selectedGcData[0]['reqap_remarks']"></a-input>
                    </a-form-item>
                    <a-form-item label="AR #">
                        <a-input v-model:value="selectedGcData[0]['spexgc_payment_arnum']"></a-input>
                    </a-form-item>
                    <a-form-item label="Payment Type">
                        <a-input :value="paymentTypeText" readonly></a-input>
                    </a-form-item>
                    <a-form-item label="Amount">
                        <a-input v-model:value="selectedGcData[0]['spexgc_payment']"></a-input>
                    </a-form-item>
                </a-card>
            </a-col>
            <a-col :span="6">
                <a-card>
                    <a-form-item label="Date Approved">
                        <a-input v-model:value="selectedGcData[0]['requestApproved']"></a-input>
                    </a-form-item>
                    <a-form-item label="Checked By">
                        <a-input v-model:value="selectedGcData[0]['reqap_checkedby']"></a-input>
                    </a-form-item>
                    <a-form-item label="Prepared By">
                        <a-input v-model:value="selectedGcData[0]['preparedBy']"></a-input>
                    </a-form-item>
                </a-card>
            </a-col>
            <a-col :span="6">
                <a-card>
                    <a-form-item label="Prepared By">
                        <a-input v-model:value="selectedGcData[0]['spexgc_remarks']"></a-input>
                    </a-form-item>
                    <a-form-item label="Approved By">
                        <a-textarea v-model:value="selectedGcData[0]['reqap_approvedby']"></a-textarea>
                    </a-form-item>
                </a-card>
            </a-col>
        </a-row>
    </a-modal>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,

    props: {
        data: Object,
        columns: Object,
        selectedGcData:Object
    },
    data() {
        return {
            open: false,
            details: '',
        }
    },
    methods: {
        approvedSpexGcDetails(data) {
            this.open = true;
            this.$inertia.get(route('finance.approvedGc.approved'),{
                id: data.spexgc_id
            },{
                preserveState: true
            })
        }
    },
    computed: {
        paymentTypeText() {
            const paymentMap = {
                '1': 'Cash',
                '2': 'Check',
                '3': 'JV',
                '4': 'AR',
                '5': 'On Account'
            };
            return paymentMap[this.selectedGcData[0]['spexgc_paymentype']] || '';
        }
    }
}

</script>