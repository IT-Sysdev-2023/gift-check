<template>
    <a-card title="Approved Production Request">
        <a-table :dataSource="data" :columns="columns">
            <template v-slot:bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'View'">
                    <a-button @click="getSelectedData(record.pe_id)">View</a-button>
                </template>
            </template>
        </a-table>
    </a-card>


    <a-modal v-model:open="open" title="Production Details" width="95%" style="top: 65px;">
        {{selectedData}}
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-card>
                    <a-form>
                        <a-form-item label="PR No">
                            <a-input v-model:value="selectedData" readonly/>
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card> {{data}}</a-card>
            </a-col>
        </a-row>
        <a-card class="mt-5">
            <a-table :dataSource="barcodes" :columns="barcodeColumns" :pagination="false" />
        </a-card>
        <template #footer>
            <a-button key="back" @click="handleCancel">Return</a-button>
            <a-button key="submit" type="primary" :loading="loading" @click="handleOk">Submit</a-button>
        </template>
    </a-modal>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

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
        }
    }
}
</script>