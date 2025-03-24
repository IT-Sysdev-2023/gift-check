<template>
    <a-alert v-if="response.status === 'error'" class="mb-3" :message="response.title" :description="response.msg"
        :type="response.status" show-icon />
    <div class="flex justify-between">
        <div>
            <a-form class="ml-1" :layout="'horizontal'">
                <a-form-item label="Count">
                    <a-input style="width: 120px; color: blue;" :value="checkedRecord.length" disabled readonly
                        class="text-center font-bold" />
                </a-form-item>
            </a-form>
        </div>
        <div>
            <a-checkbox :checked="allSelected" class="mb-3" @change="toggleSelectAll">Select All</a-checkbox>
        </div>
    </div>
    <a-table :loading="isFetching" :row-selection="rowSelection" size="small" bordered :data-source="selected.record"
        :columns="columns">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'action'">
                <a-switch size="small" v-model:checked="record.checked" @change="toggleRow(record)">
                    <template #checkedChildren><check-outlined /></template>
                    <template #unCheckedChildren><close-outlined /></template>
                </a-switch>
            </template>
        </template>
    </a-table>
    <a-descriptions bordered class="mt-6 ml-1" size="small" layout="horizontal">
        <a-descriptions-item style="width: 50%;" :span="3" label="Total Gc"> <a-tag style="font-size: 14px;"
                color="blue">
                <p>{{ selected.total }}
                    pc's</p>
            </a-tag></a-descriptions-item>
        <a-descriptions-item style="width: 50%;" label="Released By">
            <a-tag style="font-size: 14px;" color="blue">
                <p>{{ $page.props.auth.user.full_name
                    }}</p>
            </a-tag></a-descriptions-item>
    </a-descriptions>
    <a-descriptions class="ml-1" bordered size="small" layout="horizontal">
        <a-descriptions-item style="width: 50%;" :span="3" label="Payment Date">
            <p class="underline">{{ dayjs().format('MMM DD, YYYY')
                }}</p>
        </a-descriptions-item>
    </a-descriptions>

    <a-card class="mt-1">
        <div class="mt-2">
            <a-typography-text keyboard>Check by</a-typography-text><span class="text-red-500">*required</span>
            <a-form-item has-feedback :validate-status="error.checkedby ? 'error' : ''" :help="error.checkedby">
                <a-input v-model:value="form.checkedby" placeholder="Enter Checked By"
                    @change="() => error.checkedby = ''">
                </a-input>
            </a-form-item>
        </div>
        <a-typography-text keyboard>Received By</a-typography-text><span class="text-red-500">*required</span>
        <a-form-item has-feedback :validate-status="error.receiveby ? 'error' : ''" :help="error.receiveby">
            <a-input v-model:value="form.receiveby" placeholder="Type here..." @change="() => error.receiveby = ''" />
        </a-form-item>
        <a-typography-text keyboard>Select Status</a-typography-text><span class="text-red-500">*required</span>
        <a-form-item has-feedback :validate-status="error.status ? 'error' : ''" :help="error.status">
            <a-select ref="select" v-model:value="form.status" placeholder="Select Status"
                @change="() => error.status = ''">
                <a-select-option value="partial">PARTIAL</a-select-option>
                <a-select-option value="whole">WHOLE</a-select-option>
            </a-select>
        </a-form-item>
        <a-typography-text keyboard>Remarks</a-typography-text><span class="text-red-500">*required</span>
        <a-descriptions-item style="width: 50%;" label="Remarks">
            <a-textarea v-model:value="form.remarks" :rows="2" placeholder="Type here..." />
        </a-descriptions-item>
    </a-card>

    <div class="mt-3">
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="8">
                    <a-typography-text keyboard>Payment Type</a-typography-text><span class="text-red-500">*required</span>
                    <a-form-item has-feedback :validate-status="error.payment ? 'error' : ''" :help="error.payment">
                        <a-select style="width: 100%;" v-model:value="form.payment" ref="select"
                            placeholder="Select Payment Method" @change="handlePaymentMehthod">
                            <a-select-option value="0">CASH</a-select-option>
                            <a-select-option value="1">CHEQUE</a-select-option>
                            <a-select-option value="2">JV</a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
                <a-col :span="16">
                    <a-card style="width: 100%;" v-if="paymentstat.cash">
                        <p class="text-center mb-5 font-bold">CASH</p>
                        <p>Cash Amount <span class="text-red-500">*required</span></p>
                        <a-form-item has-feedback :validate-status="error.amount ? 'error' : ''" :help="error.amount">
                            <a-input-number placeholder="Enter here..." style="width: 100%;" v-model:value="form.amount"
                                @change="handleChange" :formatter="(value) =>
                                    `₱ ${value}`.replace(
                                        /\B(?=(\d{3})+(?!\d))/g,
                                        ','
                                    )
                                    " />
                        </a-form-item>
                        <a-descriptions size="small" class="mt-4" layout="vertical" bordered>
                            <a-descriptions-item style="width: 100%;">
                                <template #label>
                                    Number Into Words
                                </template>
                                <a-badge status="processing" class="font-bold font-italic" :text="form.numToWords" />
                            </a-descriptions-item>
                        </a-descriptions>

                    </a-card>

                    <a-card style="width: 100%;" v-if="paymentstat.check">
                        <p class="text-center mb-5 font-bold">CHECK</p>
                        <p class="mt-2 ml-1">
                            Bank Name <span class="text-red-500">*required</span>
                        </p>
                        <a-form-item has-feedback :validate-status="error.bank ? 'error' : ''" :help="error.bank">
                            <a-input v-model:value="form.bank" placeholder="Enter here..." />

                        </a-form-item>
                        <p class="mt-2 ml-1">
                            Account Number <span class="text-red-500">*required</span>
                        </p>
                        <a-form-item has-feedback :validate-status="error.account ? 'error' : ''" :help="error.account">

                            <a-input v-model:value="form.account" placeholder="Enter here..." />
                        </a-form-item>
                        <p class="mt-2 ml-1">
                            Check Number <span class="text-red-500">*required</span>
                        </p>
                        <a-form-item has-feedback :validate-status="error.checkno ? 'error' : ''" :help="error.checkno">
                            <a-input placeholder="Enter here..." v-model:value="form.checkno" />
                        </a-form-item>
                        <p class="mt-2 ml-1">
                            Check Amount <span class="text-red-500">*required</span>
                        </p>
                        <a-form-item has-feedback :validate-status="error.amount ? 'error' : ''" :help="error.amount">
                            <a-input-number placeholder="Enter here..." style="width: 100%;" v-model:value="form.amount"
                                @change="handleChange" :formatter="(value) =>
                                    `₱ ${value}`.replace(
                                        /\B(?=(\d{3})+(?!\d))/g,
                                        ','
                                    )
                                    " />
                        </a-form-item>
                        <a-descriptions size="small" class="mt-4" layout="horizontal" bordered>
                            <a-descriptions-item :span="2" style="width: 50%;">
                                <template #label>
                                    Number Into Words
                                </template>
                                <a-badge status="processing" class="font-bold font-italic" :text="form.numToWords" />
                            </a-descriptions-item>
                        </a-descriptions>
                    </a-card>

                    <a-card style="width: 100%;" v-if="paymentstat.jv">
                        <p class="text-center mb-5 font-bold">JV</p>
                        <p>
                            *Customer Name
                        </p>
                        <a-input style="font-weight: bold;" :value="accname" />
                    </a-card>
                </a-col>
            </a-row>
        </a-card>
    </div>
    <div class=" mt-6">
        <a-button size="large" block type="primary" @click="submit">
            <template #icon>
                <FastForwardOutlined />
            </template>
            Release Special Gc Payments
        </a-button>
    </div>
</template>

<script setup>
import { notification } from 'ant-design-vue';
import { ref, onMounted, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import dayjs from 'dayjs';
import { toWords } from 'number-to-words';

const emit = defineEmits(['updatedCount']);

const props = defineProps({
    id: Number,
    accname: String,
    balance: Number,
});

const form = useForm({
    balance: props.balance,
    id: props.id,
    payment: null,
    numToWords: null,
    amount: null,
    account: null,
    checkno: null,
    remarks: null,
    receiveby: null,
    checkedby: null,
    status: null,
    jv: props.accname,
    checked: [],
    bank: null,
});


const paymentstat = ref({
    cash: false,
    check: false,
    jv: false,
});

const handlePaymentMehthod = (key) => {

    error.value.payment = '';

    if (error.value.payment) {
        error.value = [];
    }

    if (key === '0') {
        paymentstat.value.cash = true;
        paymentstat.value.check = false;
        paymentstat.value.jv = false;
    }
    if (key === '1') {
        paymentstat.value.check = true;
        paymentstat.value.cash = false;
        paymentstat.value.jv = false;
    }
    if (key === '2') {
        paymentstat.value.jv = true;
        paymentstat.value.check = false;
        paymentstat.value.cash = false;
    }
}

const selected = ref({});
const error = ref({});
const response = ref({});
const checkedRecord = ref([]);
const isFetching = ref(false);
const allSelected = ref(false);

const columns = ref([
    {
        title: 'First Name',
        dataIndex: 'spexgcemp_fname',
        key: 'name',
    },
    {
        title: 'Last Name',
        dataIndex: 'spexgcemp_lname',
        key: 'name',
    },
    {
        title: 'Ext',
        dataIndex: 'spexgcemp_extname',
        key: 'name',
    },
    {
        title: 'Denomination',
        dataIndex: 'spexgcemp_denom',
        key: 'name',
    },
    {
        title: 'Barcode',
        dataIndex: 'spexgcemp_barcode',
        key: 'name',
    },
    {
        title: 'Action',
        key: 'action',
        align: 'center',
    },
]);

const fetchData = () => {

    isFetching.value = true;

    axios.get(route('accounting.payment.fetch', props.id)).then(res => {

        selected.value = res.data;
        isFetching.value = false;

        emit('updatedCounts', res.data.denomcount);

    });
}

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

    if (response.value.status === 'error') {
        response.value = [];
    }

    if (record.checked) {
        checkedRecord.value.push(record);
    } else {
        checkedRecord.value = checkedRecord.value.filter(item => item !== record);
    }
    allSelected.value = selected.value.record.every(row => row.checked);
    form.checked = checkedRecord.value;
};
const handleChange = (value) => {
    error.value.amount = '';
    form.numToWords = value === null ? '' : toWords(value) + ' pesos';
}

const submit = () => {
    form.transform((data) => ({ ...data })).post(route('accounting.payment.submit'), {
        onSuccess: (e) => {

            response.value = e.props.flash;

            notification[e.props.flash.status]({
                message: e.props.flash.title,
                description: e.props.flash.msg,
            });

        },
        onError: (e) => {
            error.value = e;
        },
        preserveState: true,
        preserveScroll: true,
    })
}

onMounted(() => {
    fetchData();
});

</script>
