<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { notification } from 'ant-design-vue';
import { reactive, ref, createVNode } from 'vue';
import axios from 'axios';
import dayjs from 'dayjs';
import { Modal } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { watchEffect } from 'vue';

const props = defineProps({
    store: Object,
});

const form = reactive({
    dataType: '',
    storeSelected: '',
    dateSelected: '',
    errors: {},
});


// submit function
const data = ref([]);
const count = ref(0);

const submitButton = async () => {
    form.errors = {};

    const requiredFields = {
        dataType: 'Data type field is required',
        storeSelected: 'Store field is required',
        dateSelected: 'Date field is required',
    };

    Object.entries(requiredFields).forEach(([field, message]) => {
        if (!form[field]) {
            form.errors[field] = message;
        }
    });

    if (Object.keys(form.errors).length > 0) {
        return;
    }

    try {
        const formattedDate = dayjs(form.dateSelected).format('YYYY-MM-DD');
        const response = await axios.post(route('storeaccounting.billingReportPerDay'), {
            dataType: form.dataType,
            storeSelected: form.storeSelected,
            dateSelected: formattedDate,
        });
        if (response.data.success) {
            data.value = response.data.data;
            count.value = response.data.count;
            notification.success({
                description: response.data.message
            });
        }
        if (response.data.error) {
            notification.error({
                description: response.data.message
            });
        }
    } catch (error) {
        console.error('Failed to submit', error);
        notification.error({
            description: 'Failed to submit the form. Please try again.',
        });
    }
};

const columns = ref([
    {
        title: 'Date Purchased',
        dataIndex: 'vs_date',
        customRender: ({text}) => dayjs(text).format('MMMM D, YYYY'),
    },
    {
        title: 'Barcode',
        dataIndex: 'seodtt_barcode',
    },
    {
        title: 'Denomination',
        dataIndex: 'vs_tf_denomination',
    },
    {
        title: 'Amount Redeem',
        dataIndex: 'seodtt_credpuramt',
    },
    {
        title: 'Balance',
        dataIndex: 'seodtt_balance',

    },
    {
        title: 'Customer Name',
        dataIndex: 'vs_fullname',
    },
    {
        title: 'Store Purchased',
        dataIndex: 'store_name',
    },
    {
        title: 'Transaction #',
        dataIndex: 'seodtt_transno',
    },
    {
        title: 'Store Redeem',
        dataIndex: 'store_name',
    },
    {
        title: 'Terminal #',
        dataIndex: 'seodtt_terminalno',
    },
    {
        title: 'Staff Name',
        dataIndex: 'staff_name'
    },
    {
        title: 'Validation',
        dataIndex: 'valid_type',
    },
    {
        title: 'GC Type',
        dataIndex: 'vs_gctype',
    },
    {
        title: 'GC Type Verified',
        dataIndex: 'full_date',
    },

])

const loadingEffect = ref(false);
const progressPercent = ref(0);

// Generate Excel
const generateExcel = async () => {
    Modal.confirm({
        title: 'Confirmation',
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode(
            'div',
            { style: 'color:red;' },
            'Are you sure to generate report ?',
        ),
        async onOk() {
            try {
                loadingEffect.value = true;
                progressPercent.value = 0;
                const formatted = dayjs(form.dateSelected).format('YYYY-MM-DD');
                const response = await axios.post(route('storeaccounting.reports.generateBillingPerDayReport'), {
                    date: formatted,
                    data: data.value
                }, {
                    responseType: 'blob'
                });

                progressPercent.value = 100;

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'Billing_Report_Per_Day.xlsx');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                notification.success({
                    description: 'Report generated successfully'
                });
            } catch (error) {
                console.error('Failed to generate report', error);
                notification.error({
                    description: 'Failed to generate report'
                });
            } finally {
                setTimeout(() => {
                    loadingEffect.value = false;
                    progressPercent.value = 0;
                }, 1000);
            }
        },
        onCancel() {
            console.log('Cancel');
        },
        class: 'test',
    });
};

watchEffect(() => {
    if (loadingEffect.value) {
        let progress = 0;
        const interval = setInterval(() => {
            if (progress < 99) {
                progress += 10;
                progressPercent.value = progress;
            } else {
                clearInterval(interval);
            }
        }, 100);
        watchEffect(() => {
            if (!loadingEffect.value) {
                clearInterval(interval);
                progressPercent.value = 0;
            }
        });
    }
});

</script>

<template>
    <AuthenticatedLayout>

        <header class="text-black font-bold text-lg text-center mb-6">Billing Per Day</header>
        <section class="flex items-center w-full max-w-3xl mx-auto gap-5 mt-5">
            <!-- Data Type -->
            <div class="w-full">
                <p class="font-bold">Data Type:</p>
                <a-form-item :validate-status="form.errors.dataType ? 'error' : ''" :help="form.errors.dataType"
                    class="mt-2">
                    <a-select v-model:value="form.dataType" placeholder="Select Data Type" class="w-full">
                        <a-select-option value=""><span class="text-gray-400">Select</span></a-select-option>
                        <a-select-option value="store-sales">Store Redemption/Verification</a-select-option>
                    </a-select>
                </a-form-item>
            </div>

            <!-- Store -->
            <div class="w-full">
                <p class="font-bold">Store:</p>
                <a-form-item :validate-status="form.errors.storeSelected ? 'error' : ''"
                    :help="form.errors.storeSelected" class="mt-2">
                    <a-select v-model:value="form.storeSelected" placeholder="Select Store" class="w-full">
                        <a-select-option value=""><span class="text-gray-400">Select</span></a-select-option>
                        <a-select-option v-for="item in props.store" :key="item.store_id" :value="item.store_id">
                            {{ item.store_name }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
            </div>

            <!-- Date Selected -->
            <div class="w-full">
                <p class="font-bold">Select Date:</p>
                <a-form-item :validate-status="form.errors.dateSelected ? 'error' : ''" :help="form.errors.dateSelected"
                    class="mt-2">
                    <a-date-picker v-model:value="form.dateSelected" class="w-full" />
                </a-form-item>
            </div>

            <!-- Submit Button -->
            <div class="w-full">
                <a-button @click="submitButton" type="primary" class="w-full">Submit</a-button>
            </div>
        </section>

        <!-- Loading effect  -->
        <section>
            <div v-if="loadingEffect" class="loading-container">
                <a-progress type="circle" :stroke-color="{ '0%': '#108ee9', '100%': '#87d068' }"
                    :percent="progressPercent" status="active" />
                <p>Generating Report...</p>
            </div>
        </section>

        <!-- Table Section -->
        <a-card>
            <section class="mt-8 text-center text-gray-600">
                <div>
                    <p class="text-gray-800 font-bold">Table showing billing reports per day</p>
                    <a-table size="small" :data-source="data" :columns="columns" class="mt-4">
                    </a-table>
                </div>
            </section>
        </a-card>
        <!-- generate button with the counting of the data  -->
        <section>
            <div v-if="data.length > 0"
                style="position: fixed; bottom: 80px; right: 24px; display: flex; flex-direction: column; align-items: center;">
                <a-badge :count="count" style="margin-bottom: 5px; margin-right: 5px; z-index: 100;" />
                <a-float-button class="w-14 h-14" @click="generateExcel" type="primary" title="Generate report">
                    <template #icon>
                        <FileExcelOutlined />
                    </template>
                </a-float-button>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
<style scoped>
.loading-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    z-index: 9999;
    backdrop-filter: blur(5px);
}
</style>
