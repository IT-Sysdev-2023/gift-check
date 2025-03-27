<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import dayjs from 'dayjs';
import { ref } from 'vue';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps<{
    data: {
        data: dataItems[];
        review: reviewItems[];
        approved: approvedItems[];
        reqDetails: requestedItems[];
        barcode: {
            data: object;
        }
    }
    title: string
}>();

const activeKey = ref<string>('1');

const columns = ([
    {
        title: 'Barcode',
        dataIndex: 'dti_barcode'
    },
    {
        title: 'Denomination',
        dataIndex: 'dti_denom'
    },
    {
        title: 'Lastname',
        dataIndex: 'lname'
    },
    {
        title: 'Firstname',
        dataIndex: 'fname'
    },
    {
        title: 'Middlename',
        dataIndex: 'mname'
    },
    {
        title: 'Name Ext.',
        dataIndex: 'extname'
    },
])
// review part
interface reviewItems {
    dti_date: string,
    dti_remarks: string,
    dti_preparedby: string
}

const review = computed(() => {
    const items = props.data.review[0] as reviewItems;
    return {
        dti_date: items.dti_date ?? '',
        dti_remarks: items.dti_remarks ?? '',
        dti_preparedby: items.dti_preparedby ?? ''
    }
})
// released part
interface dataItems {
    dti_remarks: string,
    dti_date: string,
    dti_approvedby: string
    dti_preparedby: string
}
const form = computed(() => {
    const items = props.data.data[0] as dataItems;
    return {
        dti_remarks: items.dti_remarks ?? '',
        dti_date: items.dti_date ?? '',
        dti_approvedby: items.dti_approvedby ?? '',
        dti_preparedby: items.dti_preparedby ?? ''
    }
})

// approved part
interface approvedItems {
    dti_date: string,
    dti_remarks: string,
    dti_doc: string,
    dti_checkby: string,
    dti_preparedby: string,
    dti_approvedby: string
}

const approved = computed(() => {
    const items = props.data.approved[0] as approvedItems;
    return {
        dti_date: items.dti_date ?? '',
        dti_remarks: items.dti_remarks ?? '',
        dti_doc: items.dti_doc ?? '',
        dti_checkby: items.dti_checkby ?? '',
        dti_preparedby: items.dti_preparedby ?? '',
        dti_approvedby: items.dti_approvedby ?? ''
    }
})

interface requestedItems {
    dti_datereq: string,
    dti_reqby: string,
    dti_dateneed: string,
    dti_remarks: string,
    dti_paymenttype: string
}
const requested = computed(() => {
    const items = props.data.reqDetails[0] as requestedItems;
    return {
        dti_datereq: items.dti_datereq ?? '',
        dti_reqby: items.dti_reqby ?? '',
        dti_dateneed: items.dti_dateneed ?? '',
        dti_remarks: items.dti_remarks ?? '',
        dti_paymenttype: items.dti_paymenttype ?? ''
    }
})
</script>
<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item><a :href="route('treasury.dashboard')">Home</a></a-breadcrumb-item>
            <a-breadcrumb-item><a :href="route('treasury.transactions.dti.dtiReleasedGc')">DTI Released
                    GC</a></a-breadcrumb-item>
            <a-breadcrumb-item>
                {{ props.title }}
            </a-breadcrumb-item>
        </a-breadcrumb>
        <a-card class="mt-5">
            <div class="card-container">
                <a-tabs v-model:activeKey="activeKey" type="card">
                    <a-tab-pane key="1" tab="DTI Released View">
                        <a-descriptions :labelStyle="{ fontWeight: 'bold' }" layout="vertical" title="Requested Details"
                            bordered class="mt-5">
                            <a-descriptions-item label="Date Requested">
                                {{ dayjs(requested.dti_datereq).format('MMMM D, YYYY') }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Requested By">
                                {{ requested.dti_reqby }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Date Validity">
                                {{ dayjs(requested.dti_dateneed).format('dddd, MMMM D YYYY, h:mm A') }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Remarks">
                                {{ requested.dti_remarks }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Payment Type">
                                {{ requested.dti_paymenttype }}
                            </a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions :labelStyle="{ fontWeight: 'bold' }" layout="vertical" title="Reviewed Details"
                            bordered class="mt-5">
                            <a-descriptions-item label="Date Reviewed">
                                {{ dayjs(review.dti_date).format('MMMM D, YYYY') }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Remarks">
                                {{ review.dti_remarks }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Reviewed By">
                                {{ review.dti_preparedby }}
                            </a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions :labelStyle="{ fontWeight: 'bold' }" layout="vertical" title="Released Details"
                            bordered class="mt-5">
                            <a-descriptions-item label="Date Released">
                                {{ dayjs(form.dti_date).format('MMMM D, YYYY') }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Remarks">
                                {{ form.dti_remarks }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Received By">
                                {{ form.dti_approvedby }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Released By">
                                {{ form.dti_preparedby }}
                            </a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions :labelStyle="{ fontWeight: 'bold' }" layout="vertical" title="Approved Details"
                            bordered class="mt-5">
                            <a-descriptions-item label="Date Reviewed">
                                {{ dayjs(approved.dti_date).format('MMMM D, YYYY') }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Documents">
                                <a-image :src="'/storage/' + approved.dti_doc" style="width: 200px; height: 100px" />
                            </a-descriptions-item>
                            <a-descriptions-item label="Checked By">
                                {{ approved.dti_checkby }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Prepared By">
                                {{ approved.dti_preparedby }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Remarks">
                                {{ approved.dti_remarks }}
                            </a-descriptions-item>
                            <a-descriptions-item label="Approved By">
                                {{ approved.dti_approvedby }}
                            </a-descriptions-item>
                        </a-descriptions>
                    </a-tab-pane>
                    <a-tab-pane key="2" tab="Barcode">
                        <a-table :columns="columns" :data-source="props.data.barcode.data" :pagination="false" />
                        <pagination :datarecords="props.data.barcode" class="mt-5" />
                    </a-tab-pane>
                </a-tabs>
            </div>
        </a-card>
        <!-- {{ approved.dti_doc }} -->
    </AuthenticatedLayout>
</template>
