<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-descriptions size="small" title="Dti Releasing Form" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" :span="3" label="RFSEGC#">{{ record.dti_num
                    }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Department">{{ record.title }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Date and Time Requested">{{ record.dti_datereq
                    }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Date Needed">{{ record.dti_dateneed }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Customer">{{ record.customer }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Payment type">
                        <a-badge status="processing" text="Ar" />
                    </a-descriptions-item>
                    <a-descriptions-item :span="3" label="Payment Amount">{{ record.totalDenom }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Requested Remarks">{{ record.dti_remarks
                    }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Requested By">{{ record.recby }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Date Approved">{{ record.dti_approveddate
                    }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Approved Document">
                        <a-image style="height: 150px; width: 150px; border-radius: 1rem;"
                            :src="'/storage/' + record.appdocs">

                        </a-image>
                    </a-descriptions-item>
                    <a-descriptions-item :span="3" label="Approved Remarks">{{ record.apremarks }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Approved By">{{ record.approvedby }}</a-descriptions-item>
                    <a-descriptions-item label="Prepared By" :span="3">{{ record.cby }}</a-descriptions-item>
                </a-descriptions>
            </a-col>
            <a-col :span="12">
                <a-card>
                    <div class="flex justify-center">

                        <a-statistic class="text-center" title="Total Denomination" :value="record.totalDenom"
                            style="margin-right: 50px" />

                    </div>
                    <br>
                    <a-card class="text-center mb-2" title="Denomination Details">
                        <a-table size="small" bordered :data-source="dataDenom" :columns="columns">
                        </a-table>
                    </a-card>

                    <a-typography-text keyboard>Checked By</a-typography-text>
                    <a-select class="mb-3" placeholder="Select Checked By" ref="select" v-model:value="form.checkby"
                        style="width: 100%" :options="options"></a-select>
                    <a-typography-text keyboard>Remarks</a-typography-text>
                    <a-textarea class="mb-3" v-model:value="form.remarks" placeholder="Input Remarks..." />
                    <a-typography-text keyboard>Received</a-typography-text>
                    <a-input class="mb-3" v-model:value="form.receivedby" placeholder="Input Received By..." />
                    <a-button size="large" class="mt-5" type="primary" block @click="submit">
                        Submit for Releasing
                    </a-button>
                </a-card>
            </a-col>
        </a-row>
        <a-card class="mt-2">
            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <a-descriptions class="text-center" size="small" title="More Details" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Gc Count" :span="3">{{ record.countBcode
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Denomination Total" :span="5">{{ record.totalDenom
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Released By" :span="5">{{
                            page.auth.user.full_name }}</a-descriptions-item>
                    </a-descriptions>
                </a-col>
                <a-col :span="12">

                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { PageWithSharedProps } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { SelectProps } from 'ant-design-vue';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import { notification } from 'ant-design-vue';

const page = usePage<PageWithSharedProps>().props;

interface Record {
    dti_num: number,
    dti_company: number,
    dti_dateneed: string,
    id: number,
    dti_datereq: string,
    recby: string,
    approvedby: string,
    reviewedby: string,
    totalDenom: number,
    dti_paymenttype: string,
    dti_approveddate: string,
    title: string,
    customer: string,
    dti_remarks: string,
    apremarks: string,
    appdocs: string,
    cby: string,
    countBcode: number,
}
const dataDenom = ref([]);

const props = defineProps<{
    record: Record,
    cbyoptions: {
        label: string,
        value: number,
    }[],
}>();

const form = useForm({
    checkby: null as string,
    remarks: null as string,
    receivedby: null as string,
});

const activeKey = ref('0');
const columns = ref([
    {
        title: 'Last Name',
        dataIndex: 'fname',
        key: 'name',
    },
    {
        title: 'First Name',
        dataIndex: 'lname',
        key: 'name',
    },
    {
        title: 'Middle Name',
        dataIndex: 'mname',
        key: 'name',
    },
    {
        title: 'Denomination',
        dataIndex: 'dti_denom',
        key: 'name',
    },
]);

const options = ref<SelectProps['options']>(props.cbyoptions);

const fetchDenomination = async () => {
    const { data } = await axios.get(route('treasury.special.gc.viewDtiDenomination', props.record.dti_num));
    dataDenom.value = data.records;
}
const submit = () => {
    form.post(route('treasury.special.gc.releasingSubmissionDti', props.record.dti_num), {
        onSuccess: (res) => {
            notification['success']({
                message: 'Success',
                description:
                    'Successfully Release Reviewed GC',
            });
            window.location.href = route('treasury.special.gc.gcReleasingDti');
        },
        onError: (err) => {
            notification['success']({
                message: 'Error',
                description:
                    'Something Went Wrong!',
            });
        }
    });
}
onMounted(() => {
    fetchDenomination();
});
</script>
