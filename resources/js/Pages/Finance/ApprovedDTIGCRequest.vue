<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/core';
import axios from 'axios';
import { defineProps } from 'vue';
import { ref } from 'vue';

const activeKey = ref('1');

const props = defineProps({
    data: Object,
    search: String,
})

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });
};

const columns = ref([
    {
        title: 'RFSEGC #',
        dataIndex: 'dti_num'
    },
    {
        title: 'Date Requested',
        dataIndex: 'dti_datereq',
        customRender: ({ text }) => formatDate(text)
    },
    {
        title: 'Date Validity',
        dataIndex: 'dti_datereq',
        customRender: ({ text }) => formatDate(text)
    },
    {
        title: 'Customer',
        dataIndex: 'dti_customer'
    },
    {
        title: 'Date Approved',
        dataIndex: 'dti_approveddate',
        customRender: ({ text }) => formatDate(text)
    },
    {
        title: 'Approved By',
        dataIndex: 'dti_approvedby'
    },
    {
        title: 'View',
        dataIndex: 'action'
    },
])
const form = ref({
    totalDenomination: '',
    dti_datereq: '',
    dti_remarks: '',
    approved_remarks: '',
    dti_paymenttype: '',
    dti_approveddate: '',
    dti_checkby: '',
    dti_approvedby: '',
    dti_doc: '',
})

const openModal = ref(false);
const barcode = ref([]);

const

    viewApprovedDti = async (data) => {
    form.value = {
        dti_datereq: data.dti_datereq ? formatDate(data.dti_datereq) : '',
        first_remarks: data.dti_remarks || '',
        approved_remarks: data.approved_remarks || '',
        dti_paymenttype: data.dti_paymenttype || '',
        dti_approveddate: data.dti_approveddate ? formatDate(data.dti_approveddate) : '',
        dti_checkby: data.dti_checkby || '',
        dti_approvedby: data.dti_approvedby || '',
        dti_doc: data.dti_doc || '',
        totalDenomination: data.totalDenomination || ''
    }

    await axios.get(route('finance.approvedGc.selected.dti.request'), {
        params: {
            id: data.dti_num,
        }
    })
        .then((response) => {
            openModal.value = true;
            barcode.value = response.data.barcodes;
        });
}

const approvedSearch = ref(props.search);
const searchValue = () => {
    router.get(route('finance.request.approve'), {
        search: approvedSearch.value,
    }, {
        preserveState: true,
    });
}

const barcodeColumns = ref([
    {
        title: 'Barcode',
        dataIndex: 'dti_barcode',
    },
    {
        title: 'Denomination',
        dataIndex: 'dti_denom'
    },
    {
        title: 'Fullname',
        dataIndex: 'fullname'
    }
])
</script>
<template>
    <AuthenticatedLayout>
        <a-card>
            <a-card>
                <div class="flex items-center">
                    <p class="text-lg">Approved DTI Special GC Request</p>
                    <Link :href="route('finance.dashboard')" class="ml-auto text-black text-blue-700">
                    <RollbackOutlined /> Back to Dashboard
                    </Link>
                </div>
            </a-card>
            <main class="mt-5">
                <div class="flex justify-end">
                    <a-input-search class="lg:w-[350px]" enter-button allow-clear placeholder="Input search here..."
                        v-model:value="approvedSearch" @change="searchValue" />
                </div>
                <section>
                    <a-table :data-source="props.data.data" :columns="columns" size="small" class="mt-5"
                        :pagination="false">
                        <template #bodyCell="{ record, column }">
                            <template v-if="column.dataIndex === 'action'">
                                <a-button type="primary" @click="viewApprovedDti(record)">
                                    <PicLeftOutlined /> View
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                    <pagination :datarecords="props.data" class="mt-5" />
                </section>
            </main>
        </a-card>
        <a-modal v-model:open="openModal" width="100%" :footer="false">
            <div class="card-container">
                <a-tabs v-model:activeKey="activeKey" type="card">
                    <a-tab-pane key="1" tab="DTI Special Gc Request">
                        <div class="flex direction-columns gap-5">
                            <a-card>
                                <a-form-item label="Date Requested">
                                    <a-input readonly v-model:value="form.dti_datereq">
                                    </a-input>
                                </a-form-item>
                                <a-form-item label="Document">
                                    <img :src="form.dti_doc" alt="Document Image"
                                        style="max-width: 100px; height: auto;">
                                </a-form-item>
                            </a-card>
                            <a-card>
                                <a-form-item label="Date Validity">
                                    <a-input readonly v-model:value="form.dti_datereq">

                                    </a-input>
                                </a-form-item>
                                <a-form-item label="Remarks">
                                    <a-input readonly v-model:value="form.first_remarks">

                                    </a-input>
                                </a-form-item>
                                <a-form-item label="Payment Type">
                                    <a-input readonly v-model:value="form.dti_paymenttype">

                                    </a-input>
                                </a-form-item>
                                <a-form-item label="Amount">
                                    <a-input readonly v-model:value="form.totalDenomination">

                                    </a-input>
                                </a-form-item>
                            </a-card>
                            <a-card>
                                <a-form-item label="Date Approved">
                                    <a-input readonly v-model:value="form.dti_approveddate">

                                    </a-input>
                                </a-form-item>
                                <a-form-item label="Checked By">
                                    <a-input readonly v-model:value="form.dti_checkby">

                                    </a-input>
                                </a-form-item>
                                <a-form-item label="Prepared By">
                                    <a-input readonly v-model:value="form.dti_approvedby">

                                    </a-input>
                                </a-form-item>
                            </a-card>
                            <a-card>
                                <a-form-item label="Remarks">
                                    <a-input readonly v-model:value="form.approved_remarks">

                                    </a-input>
                                </a-form-item>
                                <a-form-item label="Approved By">
                                    <a-input readonly v-model:value="form.dti_approvedby">

                                    </a-input>
                                </a-form-item>
                            </a-card>
                        </div>
                    </a-tab-pane>
                    <a-tab-pane key="2" tab="Barcodes">
                        <a-table :columns="barcodeColumns" :data-source="barcode" size="small">

                        </a-table>
                    </a-tab-pane>
                </a-tabs>
            </div>
        </a-modal>
        <!-- {{ data }} -->
    </AuthenticatedLayout>
</template>
