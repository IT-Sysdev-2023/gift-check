<template>
    <AuthenticatedLayout>
        <a-alert class="mb-2" v-if="form.errors.checked" message="Error" description="Please Select a Data first!"
            type="error" show-icon />
        <a-card class="mb-2">
            <div class="flex justify-between mb-4">
                <div>
                    <a-form layout="inline">
                        <a-form-item label="Count">
                            <a-input :value="form.checked.length" style="width: 150px;" class="text-center"
                                placeholder="Username">
                            </a-input>
                        </a-form-item>
                    </a-form>
                </div>
                <div>
                    <a-checkbox @change="toggleSelectAll">
                        Select all
                    </a-checkbox>
                </div>
            </div>
            <a-table :loading="isFetching" bordered size="small" :data-source="selected?.record" :columns="columns">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'checkbox'">
                        <a-switch v-model:checked="record.checked" @change="toggleRow(record)">
                            <template #checkedChildren><check-outlined /></template>
                            <template #unCheckedChildren><close-outlined /></template>
                        </a-switch>
                    </template>
                </template>
            </a-table>
        </a-card>
        <a-row :gutter="[16, 16]">
            <a-col :span="10">
                <a-card>
                    <a-descriptions size="small" title="Details" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="RFSEGC#">{{ props.record.dti_num
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Department">{{ props.record.title
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Request">{{ props.record.dti_datereq
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Time Requested">{{ props.record.timerequested
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Needed">{{ props.record.dti_dateneed
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Approved">{{ props.record.dti_approveddate
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Payment Amount">{{ props.record.dti_balance
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Total Denomination">{{ selected?.denomcount
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Requested Remarks">{{ props.record.remarks
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Approved Remarks">{{ props.record.apremarks
                        }}</a-descriptions-item>
                    </a-descriptions>
                </a-card>
                <a-divider>
                    Committee
                </a-divider>
                <a-card>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Requested By">{{ props.record.refullname
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Checked By">{{ props.record.dti_checkby
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Approved By">{{ props.record.dti_approvedby
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Prepared By">{{ props.record.prefullname
                        }}</a-descriptions-item>
                    </a-descriptions>
                </a-card>

            </a-col>
            <a-col :span="14">
                <a-card title="Releasing Form">
                    <a-row :gutter="[16, 16]" class="text-center">
                        <a-col :span="12">
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item style="width: 50%;" label="Total Gift Check">{{ selected?.total
                                    }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item style="width: 50%;" label="Payment Date">{{
                                    dayjs().format('MMM, DD, YYYY') }}</a-descriptions-item>
                            </a-descriptions>
                        </a-col>
                        <a-col :span="12">
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item style="width: 50%;" label="Released By">{{
                                    page.auth.user.full_name }}</a-descriptions-item>
                            </a-descriptions>
                            <div class="text-start mt-2">
                                <a-typography-text keyboard>Checked By</a-typography-text><span
                                    class="text-red-500">*required</span>
                                <a-input placeholder="Enter Checked By" v-model:value="form.checkedby" />
                                <p class="text-start ml-1 text-red-500" v-if="form.errors.checkedby">
                                    * This field is Required
                                </p>
                            </div>
                            <div class="text-start mt-3">
                                <a-typography-text keyboard>Payment Status</a-typography-text><span
                                    class="text-red-500">*required</span>
                                <a-select placeholder="Select Payment Type" v-model:value="form.paymentstats"
                                    class="w-full" :options="paymentStatus"></a-select>
                                <p class="text-start ml-1 text-red-500" v-if="form.errors.paymentstats">
                                    * This field is Required
                                </p>
                            </div>
                            <div class="text-start mt-2">
                                <a-typography-text keyboard>Received By</a-typography-text><span
                                    class="text-red-500">*required</span>
                            </div>
                            <a-input v-model:value.lazy="form.recby" autofocus placeholder="Enter Received By" />
                            <p class="text-start ml-1 text-red-500" v-if="form.errors.recby">
                                * This field is Required
                            </p>
                        </a-col>
                        <div class="mt-2 text-start w-[97%] m-auto">
                            <a-typography-text keyboard>Remarks</a-typography-text><span
                                class="text-red-500">*required</span>
                            <a-textarea v-model:value="form.remarks" placeholder="Enter remarks here.." :row="2" />
                            <p class="text-start ml-1 text-red-500" v-if="form.errors.remarks">
                                * This field is Required
                            </p>
                        </div>
                        <div class="mt-2 text-start w-[97%] m-auto">
                            <a-typography-text keyboard>Payment Type</a-typography-text><span
                                class="text-red-500">*required</span>
                            <a-select placeholder="Select Payment Type" @change="handlePaymentTypChange" class="w-full"
                                :options="paymentOption"></a-select>
                            <p class="text-start ml-1 text-red-500" v-if="form.errors.paymentType">
                                * This field is Required
                            </p>
                        </div>

                    </a-row>
                    <div v-if="pmValue.check">
                        <a-card size="small" title="Add Check Details" class="mt-2">
                            <a-row :gutter="[16, 16]">
                                <a-col :span="12">
                                    <a-typography-text keyboard>Bank
                                        Name</a-typography-text><span
                                        class="text-red-500">*required</span>
                                    <a-input class="mb-3" v-model:value="form.bankname" placeholder="Enter here..." />
                                    <p class="text-start ml-1 text-red-500" v-if="form.errors.bankname">
                                        * This field is Required
                                    </p>
                                    <a-typography-text keyboard>Account Number</a-typography-text><span
                                    class="text-red-500">*required</span>
                                    <a-input placeholder="Enter here..." v-model:value="form.accountno" />
                                    <p class="text-start ml-1 text-red-500" v-if="form.errors.accountno">
                                        * This field is Required
                                    </p>
                                </a-col>
                                <a-col :span="12">
                                    <a-typography-text keyboard>Check Number</a-typography-text><span
                                    class="text-red-500">*required</span>
                                    <a-input class="mb-3" v-model:value="form.checkno" placeholder="Enter here..." />
                                    <p class="text-start ml-1 text-red-500" v-if="form.errors.accountno">
                                        * This field is Required
                                    </p>
                                    <a-typography-text keyboard>Check Amount</a-typography-text><span
                                    class="text-red-500">*required</span>
                                    <a-input-number style="width: 100%;" @change="handleChange" :formatter="(value) =>
                                        `₱ ${value}`.replace(
                                            /\B(?=(\d{3})+(?!\d))/g,
                                            ','
                                        )
                                        " v-model:value="form.checkamount" placeholder="Enter here..." />
                                    <p class="text-start ml-1 text-red-500" v-if="form.errors.checkamount">
                                        * This field is Required
                                    </p>
                                </a-col>
                                <div class="w-[98%] m-auto">
                                    <a-typography-text keyboard>Amount In Words</a-typography-text>
                                    <a-input :value="form.numToWords" readonly placeholder="Enter here..." />
                                </div>
                            </a-row>
                        </a-card>
                    </div>
                    <div v-else-if="pmValue.cash">
                        <a-card size="small" title="Enter Cash Amount" class="mt-2">
                            <a-typography-text keyboard>Cash Amount</a-typography-text><span
                            class="text-red-500">*required</span>
                            <a-input-number placeholder="Enter here..." style="width: 100%;" v-model:value="form.amount"
                                @change="handleChange" :formatter="(value) =>
                                    `₱ ${value}`.replace(
                                        /\B(?=(\d{3})+(?!\d))/g,
                                        ','
                                    )
                                    " />
                            <p class="text-start ml-1 text-red-500" v-if="form.errors.amount">
                                * This field is Required
                            </p>
                            <div class="mt-3">
                                <a-typography-text keyboard>Amount In Words</a-typography-text>
                                <a-input :value="form.numToWords" readonly placeholder="Enter here..." />
                            </div>
                        </a-card>
                    </div>
                    <div v-else-if="pmValue.jv">
                        <a-card size="small" title="Customer Details" class="mt-2">
                            <a-typography-text keyboard>Customer Name</a-typography-text><span
                            class="text-red-500">*required</span>
                            <a-input v-model:value="form.custname" class="uppercase" placeholder="Enter here..." />
                        </a-card>
                    </div>
                </a-card>
                <div>
                    <a-button block class="mt-5 p-1 pb-7" type="primary" @click="submit">
                        Submit Payment
                    </a-button>
                </div>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { SelectProps } from 'ant-design-vue';
import axios from 'axios';
import dayjs from 'dayjs';
import { onMounted, ref } from 'vue';
import { toWords } from 'number-to-words';
import { notification } from 'ant-design-vue';

interface RecordDti {
    dti_num: number,
    title: string,
    dti_datereq: string,
    dti_dateneed: string,
    dti_approveddate: string,
    dti_balance: number,
    apremarks: string,
    remarks: string,
    dti_checkby: string,
    dti_approvedby: string,
    refullname: string,
    prefullname: string,
    timerequested: string,
}

const props = defineProps<{
    record: RecordDti
}>();


const page = usePage().props;

interface SelectedDti {
    record: {
        dti_denom: number,
        fname: string,
        lname: string,
        mname: string,
        extname: string,
        dti_barcode: string,
        checked: boolean,
    }[],
    total: number,
    denomcount: number,
}
const selected = ref<SelectedDti>();

const columns = ref([
    {
        title: 'First Name',
        dataIndex: 'fname',
        key: 'name',
    },
    {
        title: 'Last Name',
        dataIndex: 'lname',
        key: 'age',
    },
    {
        title: 'Ext',
        dataIndex: 'address',
        key: 'extname',
    },
    {
        title: 'Middle Name',
        dataIndex: 'mname',
        key: 'extname',
    },
    {
        title: 'Denomination',
        dataIndex: 'dti_denom',
        key: 'address',
    },
    {
        title: 'Barcode',
        dataIndex: 'dti_barcode',
        key: 'address',
    },
    {
        title: 'Action',
        key: 'checkbox',
        align: 'center',
    },
]);

const paymentOption = ref<SelectProps['options']>([
    {
        value: '1',
        label: 'Cash',
    },
    {
        value: '2',
        label: 'Cheque',
    },
    {
        value: '3',
        label: 'Jv',
    },
]);
const paymentStatus = ref<SelectProps['options']>([
    {
        value: 'partial',
        label: 'Partial',
    },
    {
        value: 'whole',
        label: 'Whole',
    },
]);

interface PmValue {
    check: boolean,
    cash: boolean,
    jv: boolean,
}

const pmValue = ref<PmValue>({
    check: false,
    cash: false,
    jv: false,
});

const handlePaymentTypChange = (value: string) => {
    form.paymentType = value;
    const keys: Record<string, keyof PmValue> = { '1': 'cash', '2': 'check', '3': 'jv' };

    Object.keys(pmValue.value).forEach((key) => {
        pmValue.value[key as keyof PmValue] = key === keys[value] ? !pmValue.value[key as keyof PmValue] : false;
    });
};

const error = ref();

const form = useForm({
    id: props.record.dti_num,
    balance: props.record.dti_balance,
    numToWords: '' as string,
    remarks: '' as string,
    amount: '',
    checked: [] as string[],
    recby: '' as string,
    paymentstats: null as string | null,
    checkedby: '' as string,
    paymentType: '' as string,
    bankname: '' as string,
    accountno: null as number,
    checkno: null as number,
    checkamount: null as number,
    custname: 'Ramiro Hospital' as string
});

const handleChange = (value: number) => {
    form.numToWords = value === null ? '' : toWords(value) + ' pesos';
}
const isFetching = ref<boolean>(false);


const fetchData = () => {

    isFetching.value = true;

    axios.get(route('accounting.payment.fetch.dti', props.record.dti_num)).then(res => {
        selected.value = res.data;
        isFetching.value = false;

    });
}

const allSelected = ref<boolean>(false);

const checkedRecord = ref<any>([]);

const toggleSelectAll = (event) => {

    allSelected.value = event.target.checked;

    selected.value.record.forEach(row => {
        row.checked = allSelected.value;
        if (allSelected.value) {
            if (!checkedRecord.value.includes(row)) {
                checkedRecord.value.push(row);
            }
        } else {
            checkedRecord.value = checkedRecord.value.filter(item => item !== row);
        }

    });

    form.checked = checkedRecord.value;
};

const toggleRow = (record) => {

    // if (response.value.status === 'error') {
    //     response.value = [];
    // }

    if (record.checked) {
        checkedRecord.value.push(record);
    } else {
        checkedRecord.value = checkedRecord.value.filter(item => item !== record);
    }
    allSelected.value = selected.value.record.every(row => row.checked);
    form.checked = checkedRecord.value;
};

const submit = () => {
    form.post(route('accounting.payment.submit.dti'), {
        onSuccess: () => {
            notification['success']({
                message: 'Success',
                description:
                    'Released Submit Successfully',
            });
        },
        onError: () => {
            notification['error']({
                message: 'Opss',
                description:
                    'Something went wrong! Check all the fields or The Informations',
            });
        }
    });
}


onMounted(() => {
    fetchData();
})

</script>
