<template>
    <AuthenticatedLayout>
        <a-card>
            <a-tabs type="card" v-model:activeKey="activeKey">
                <a-tab-pane key="1">
                    <template #tab>
                        <span>
                            <apple-outlined />
                            Special External GC Request
                        </span>
                    </template>
                    <a-row :gutter="[16, 16]">

                        <a-col :span="12">
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item :span="3" style="width: 50%;" label="Date Requested"> {{
                                    dayjs(records.spgc.spexgc_datereq).format('MMMM DD, YYYY') }}</a-descriptions-item>
                                <a-descriptions-item :span="3" style="width: 50%;" label="Requested By">{{
                                    records.spgc.user.full_name }}</a-descriptions-item>
                                <a-descriptions-item :span="3" style="width: 50%;" label="Payment Type">
                                    {{ records.spgc.spexgc_paymentype === '1' ? 'CASH' : 'CHECK' }}
                                </a-descriptions-item>

                                <a-descriptions-item v-if="records.spgc.spexgc_paymentype === '2'" :span="3"
                                    style="width: 50%;" label="Bank Name">
                                </a-descriptions-item>
                                <a-descriptions-item v-if="records.spgc.spexgc_paymentype === '2'" :span="3"
                                    style="width: 50%;" label="Bank Account Number">
                                </a-descriptions-item>
                                <a-descriptions-item v-if="records.spgc.spexgc_paymentype === '2'" :span="3"
                                    style="width: 50%;" label="Check Number">
                                </a-descriptions-item>
                                <a-descriptions-item v-if="records.spgc.spexgc_paymentype === '2'" :span="3"
                                    style="width: 50%;" label="Check Amount">
                                </a-descriptions-item>

                                <a-descriptions-item v-if="records.spgc.spexgc_paymentype === '1'" :span="3"
                                    style="width: 50%;" label="Amount">
                                    {{ records.spgc.spexgc_payment }}
                                </a-descriptions-item>

                                <a-descriptions-item :span="3" style="width: 50%;" label="Documents">
                                    <!-- {{ records.docs }} -->
                                    <div v-if="records.docs.length !== 0">
                                        <span v-for="item in records.docs">
                                            <a-image class="mt-2" :src="'/storage/' + item.doc_fullpath"
                                                alt="image"></a-image>
                                        </span>
                                    </div>
                                    <div v-else>
                                        <a-empty :image="simpleImage" />
                                    </div>
                                </a-descriptions-item>
                                <a-descriptions-item :span="3" style="width: 50%;" label="Remarks">{{
                                    records.spgc.reqap_remarks
                                    }}</a-descriptions-item>
                            </a-descriptions>

                            <div class="mt-4">
                                <a-button @click="reprint" block type="primary">
                                    Reprint this Request
                                </a-button>
                            </div>
                        </a-col>
                        <a-col :span="12">
                            <a-card>
                                <a-descriptions layout="horizontal" size="small" bordered>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Date Approved">
                                        {{ dayjs(records.spgc.spexgc_datereq).format('MMMM DD, YYYY') }}
                                    </a-descriptions-item>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Checked By">{{
                                        records.spgc.reqap_checkedby
                                        }}</a-descriptions-item>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Prepared By">{{
                                        records.spgc.firstname.charAt(0).toUpperCase() }} , {{
                                            records.spgc.lastname.charAt(0).toUpperCase()
                                        }}</a-descriptions-item>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Remarks">{{
                                        records.spgc.spexgc_remarks
                                        }}</a-descriptions-item>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Approved By">{{
                                        records.spgc.reqap_approvedby
                                        }}</a-descriptions-item>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Documents">
                                        <span v-if="records.spgc.reqap_doc !== null">
                                            <a-image :src="'/storage/' + records.spgc.reqap_doc" alt="image">
                                            </a-image>
                                        </span>
                                        <span v-else>
                                            <a-empty :image="simpleImage" />
                                        </span>
                                    </a-descriptions-item>
                                </a-descriptions>
                            </a-card>
                            <a-card class="mt-2">
                                <a-descriptions layout="horizontal" size="small" bordered>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Date Reviewed">{{
                                        records.approved.reqap_date
                                        }}</a-descriptions-item>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Remarks">{{
                                        records.approved.reqap_remarks
                                        }}</a-descriptions-item>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Reviewed By">{{
                                        records.approved?.user.full_name }}</a-descriptions-item>
                                </a-descriptions>
                            </a-card>
                            <a-card class="mt-2">
                                <a-descriptions layout="horizontal" size="small" bordered>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Date Released">{{
                                        records.released.reqap_date
                                        }}</a-descriptions-item>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Received By">{{
                                        records.spgc.spexgc_receviedby
                                        }}</a-descriptions-item>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Remarks">{{
                                        records.released.reqap_remarks
                                        }}</a-descriptions-item>
                                    <a-descriptions-item :span="3" style="width: 50%;" label="Released By">{{
                                        records.released?.user.full_name }}</a-descriptions-item>
                                </a-descriptions>
                            </a-card>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane key="2">
                    <template #tab>
                        <span>
                            <android-outlined />
                            Barcodes
                        </span>
                    </template>
                   <a-card>
                    <a-table size="small" bordered :dataSource="records.barcodes" :columns="columns" />
                   </a-card>
                </a-tab-pane>
            </a-tabs>
<!-- {{ id }} -->
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import dayjs from 'dayjs';
import { Empty } from 'ant-design-vue';
import { ref } from 'vue';
import { router } from '@inertiajs/core';
const simpleImage = Empty.PRESENTED_IMAGE_SIMPLE;

const activeKey = ref('1');

interface Spgc {
    spexgc_dateneed: string,
    spexgc_datereq: string,
    firstname: string,
    lastname: string,
    reqap_remarks: string,
    spexgc_paymentype: string,
    spexgcbi_bankname: string,
    spexgcbi_bankaccountnum: string,
    spexgcbi_checknumber: string,
    spexgc_payment: number,
    reqap_doc: string,
    spexgc_remarks: string,
    reqap_checkedby: string,
    reqap_approvedby: string,
    spexgc_receviedby: string,
    user: {
        full_name: string
    }
}
interface Docs {
    doc_fullpath: any,
    length: number,
}
interface Approved {
    reqap_date: string,
    reqap_remarks: string,
    user: {
        full_name: string
    }

}
interface Released {
    reqap_date: string,
    reqap_remarks: string,
    user: {
        full_name: string
    }

}
interface Barcodes {
    spexgcemp_trid: number,
    spexgcemp_denom: string,
    spexgcemp_fname: string,
    spexgcemp_lname: string,
    spexgcemp_mname: string,
    spexgcemp_extname: string,
    spexgcemp_barcode: string,
}
const props = defineProps<{
    records: {
        spgc: Spgc,
        docs: Docs,
        approved: Approved,
        released: Released,
        barcodes: Barcodes,
    }
    id: number,
}>();


const columns = ref([
    {
        title: 'Barcode',
        dataIndex: 'spexgcemp_barcode',
    },
    {
        title: 'Denomination',
        dataIndex: 'spexgcemp_denom',
    },
    {
        title: 'Lastname',
        dataIndex: 'spexgcemp_lname',
    },
    {
        title: 'Firstname',
        dataIndex: 'spexgcemp_fname',
    },
    {
        title: 'Middlename',
        dataIndex: 'spexgcemp_mname',
    },
    {
        title: 'Name Ext.',
        dataIndex: 'spexgcemp_extname',
    },
]);
const reprint = () => {
    router.get(route('custodian.reprint.request.released', props.id))
}
</script>
