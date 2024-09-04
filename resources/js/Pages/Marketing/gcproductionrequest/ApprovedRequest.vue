<template>
    <a-card title="Approved Production Request">
        <div class="flex justify-end">
            <a-input-search class="w-96 mb-5" v-model:value="search" 
            placeholder="Enter PR Number" enter-button @search="onSearch" />
        </div>

        <a-table :dataSource="data.data" :columns="columns" :pagination="false">
            <template v-slot:bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'View'">
                    <a-button type="primary" @click="getSelectedData(record.pe_id)"><PicLeftOutlined />View</a-button>
                </template>
            </template>
        </a-table>
        <pagination class="mt-5" :datarecords="data" />
    </a-card>


    <a-modal v-model:open="open" title="Production Details" width="95%" style="top: 65px;">
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-card>
                    <a-form>
                        <a-form-item label="PR No">
                            <a-input v-model:value="selectedData.pe_num" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested">
                            <a-input v-model:value="selectedData.DateRequested" readonly />
                        </a-form-item>
                        <a-form-item label="Date Needed">
                            <a-input v-model:value="selectedData.DateNeeded" readonly />
                        </a-form-item>
                        <a-form-item label="Request Remarks">
                            <a-input v-model:value="selectedData.pe_remarks" readonly />
                        </a-form-item>
                        <a-form-item label="Request Prepared by">
                            <a-input v-model:value="selectedData.RequestPreparedby" readonly />
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card>
                    <a-form>
                        <a-form-item label="Date Approved">
                            <a-input v-model:value="selectedData.DateApproved"></a-input>
                        </a-form-item>
                        <a-form-item label="Approved Remarks">
                            <a-input v-model:value="selectedData.ape_remarks"></a-input>
                        </a-form-item>
                        <a-form-item label="Approved by">
                            <a-input v-model:value="selectedData.ape_approved_by"></a-input>
                        </a-form-item>
                        <a-form-item label="Checked by">
                            <a-input v-model:value="selectedData.ape_checked_by"></a-input>
                        </a-form-item>
                        <a-form-item label="Prepared by">
                            <a-input v-model:value="selectedData.aprrovedPreparedBy"></a-input>
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
        </a-row>
        <a-card class="mt-5">
            <a-table :dataSource="barcodes" :columns="barcodeColumns" :pagination="false" />
        </a-card>
        <template #footer>
            <a-button key="back" @click="handleCancel">Close</a-button>
        </template>
    </a-modal>
</template>

<script>
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {PicLeftOutlined} from '@ant-design/icons-vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Object,
        barcodeColumns: Object,
        barcodes: Object,
        selectedData: Object,
    },
    data() {
        return {
            open: false,
            search: '',
            id: ''
        }
    },
    methods: {
        getSelectedData(data) {
            this.$inertia.get(route('marketing.approvedRequest.approved.request'), {
                id: data
            }, {
                onSuccess: () => {
                    this.open = true;
                },
                preserveState: true
            });
        },
        handleCancel() {
            this.open = false
        },
        onSearch(data){
            this.$inertia.get(route('marketing.approvedRequest.approved.request'),{
                search: data,
            })
        }
    }
}
</script>