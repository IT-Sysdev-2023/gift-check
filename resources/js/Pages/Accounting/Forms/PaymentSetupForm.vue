<template>
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

    <a-descriptions class="mt-6 ml-1" size="small" layout="horizontal">
        <a-descriptions-item style="width: 50%;" label="Total Gc"> <a-tag style="font-size: 14px;" color="blue">
                <p>{{ selected.total }}
                    pc's</p>
            </a-tag></a-descriptions-item>
        <a-descriptions-item style="width: 50%;" label="Released By">
            <a-tag style="font-size: 14px;" color="blue">
                <p>{{ $page.props.auth.user.full_name
                    }}</p>
            </a-tag></a-descriptions-item>
    </a-descriptions>
    <a-descriptions class="ml-1" size="small" layout="horizontal">
        <a-descriptions-item style="width: 50%;" label="Payment Date">
            <p class="underline">{{ dayjs().format('MMM DD, YYYY')
                }}</p>
        </a-descriptions-item>
        <a-descriptions-item style="width: 50%;" label="Checked by"><a-input></a-input></a-descriptions-item>
    </a-descriptions>
    <a-descriptions class="ml-1 mt-1" size="small" layout="vertical">
        <a-descriptions-item style="width: 50%;" label="Received By">
            <a-input placeholder="Type here..." />
        </a-descriptions-item>
        <a-descriptions-item style="width: 50%;" label="Payment Status">
            <a-select ref="select" placeholder="Select Status" style="width: 100%">
                <a-select-option value="jack">PARTIAL</a-select-option>
                <a-select-option value="lucy">WHOLE</a-select-option>
            </a-select>
        </a-descriptions-item>
    </a-descriptions>
    <a-descriptions class="ml-1 mt-1" size="small" layout="vertical">
        <a-descriptions-item v-model:value="form.payment" style="width: 20%;" label="Payment Type">
            <a-select ref="select" placeholder="Select Payment Method" style="width: 100%"
                @change="handlePaymentMehthod">
                <a-select-option value="0">CASH</a-select-option>
                <a-select-option value="1">CHECK</a-select-option>
                <a-select-option value="2">JV</a-select-option>
            </a-select>
        </a-descriptions-item>

        <a-descriptions-item style="width: 50%;" label="">
            <a-card style="width: 100%;" v-if="payment.cash">
                <p class="text-center mb-5 font-bold">CASH</p>
                <p>Cash Amount</p>
                <a-input-number placeholder="Enter here..." style="width: 100%;" v-model:value="form.amount"
                    @change="handleChange" />
                <a-descriptions size="small" class="mt-4" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 100%;">
                        <template #label>
                            Number Into Words
                        </template>
                        <a-badge status="processing" class="font-bold font-italic" :text="form.numToWords" />
                    </a-descriptions-item>
                </a-descriptions>

            </a-card>

            <a-card style="width: 100%;" v-if="payment.check">
                <p class="text-center mb-5 font-bold">CHECK</p>
                <p class="mt-2 ml-1">
                    Bank Name
                </p>
                <a-input placeholder="Enter here..." />
                <p class="mt-2 ml-1">
                    Account Number
                </p>
                <a-input placeholder="Enter here..." />
                <p class="mt-2 ml-1">
                    Check Number
                </p>
                <a-input placeholder="Enter here..." />
                <p class="mt-2 ml-1">
                    Check Amount
                </p>
                <a-input placeholder="Enter here..." />
                <p class="mt-4">Amount in words</p>
                <a-textarea readonly>

                </a-textarea>
            </a-card>

            <a-card style="width: 100%;" v-if="payment.jv">
                <p class="text-center mb-5 font-bold">JV</p>
                <p>
                    *Customer Name
                </p>
                <a-input style="font-weight: bold;" :value="'IBEX'" />
            </a-card>
        </a-descriptions-item>

    </a-descriptions>
    <a-descriptions class="ml-1 mt-4" size="small" layout="horizontal">
        <a-descriptions-item style="width: 50%;" label="Remarks">
            <a-textarea :rows="3" placeholder="Type here..." :maxlength="6" />
        </a-descriptions-item>
    </a-descriptions>
    <div class="flex justify-center mt-6">
        <a-button type="primary">
            <template #icon>
                <FastForwardOutlined />
            </template>
            Released Special Gc Payment
        </a-button>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import dayjs from 'dayjs';
import { toWords } from 'number-to-words';

const emit = defineEmits(['updatedCount']);

const props = defineProps({
    id: Number,
});

const form = useForm({
    payment: null,
    numToWords: null,
});


const payment = ref({
    cash: false,
    check: false,
    jv: false,
});

const handlePaymentMehthod = (key) => {
    if (key === '0') {
        payment.value.cash = true;
        payment.value.check = false;
        payment.value.jv = false;
    }
    if (key === '1') {
        payment.value.check = true;
        payment.value.cash = false;
        payment.value.jv = false;
    }
    if (key === '2') {
        payment.value.jv = true;
        payment.value.check = false;
        payment.value.cash = false;
    }
}

const selected = ref({});
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
        dataIndex: 'spexgcemp_fname',
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

// const onPaginate = async (e) => {
//     if (e.url) {
//         const { data } = await axios.get(e.url);

//         selected.value = data;

//     }

// }
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
};

const toggleRow = (record) => {
    if (record.checked) {
        checkedRecord.value.push(record);
    } else {
        checkedRecord.value = checkedRecord.value.filter(item => item !== record);
    }
    allSelected.value = selected.value.record.every(row => row.checked);
};
const handleChange = (value) => {
    form.numToWords = toWords(value);
}


onMounted(() => {
    fetchData();
});

</script>
