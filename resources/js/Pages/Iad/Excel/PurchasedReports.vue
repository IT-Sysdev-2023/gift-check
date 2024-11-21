<template>
    <AuthenticatedLayout>
        <a-card>
            <div class="flex justify-center">
                <a-select placeholder="Select Type of Report" v-model:value="selected" ref="select"
                    style="width: 400px;" :options="selectedType">
                </a-select>
            </div>
            <a-row class="mt-5" :gutter="[16, 16]">
                <a-col :span="12">
                    <a-card>
                        <a-row :gutter="[16, 16]">
                            <a-col :span="12">

                                <a-date-picker @change="handleDateChange" class="mb-2" style="width: 100%;"
                                    :picker="selected === '0' ? 'month' : 'year'" />

                                <a-button block type="primary" @click="generate">
                                    <template #icon>
                                        <PrinterOutlined />
                                    </template>
                                    Generate Excel
                                </a-button>
                            </a-col>
                            <a-col :span="12">
                                <a-select placeholder="Data Type" class="mb-2" @change="handleChangeDataType"
                                    ref="select" style="width: 100%;" :options="datatype">
                                </a-select>
                                <a-select placeholder="Select Store" v-model:value="storeData"
                                    :disabled="purchase !== 'vgc'" class="mb-5" ref="select" style="width: 100%;"
                                    :options="selectedStores">
                                </a-select>
                            </a-col>
                        </a-row>
                    </a-card>
                </a-col>
                <a-col :span="12">
                    <a-card>

                    </a-card>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">

import axios from 'axios';
import { ref, onMounted } from 'vue';
import type { Dayjs } from 'dayjs';
import type { SelectProps } from 'ant-design-vue';


const selected = ref<string>('0');

const date = ref<Dayjs>();

const purchase = ref<string>('');

const storeData = ref<number>();


const handleDateChange = (obj: any, str: any) => {
    date.value = str;
}

const handleChangeDataType = (value: string) => {
    purchase.value = value;
}

interface Records {
    stores: {
        value: number,
        label: string
    }[],
};

const props = defineProps<Records>();

const selectedStores = ref<SelectProps['options']>(props.stores);


const selectedType = ref<SelectProps['options']>([
    {
        value: '0',
        label: 'Billing Between Stores and Bu Monthly',
    },
    {
        value: '1',
        label: 'Billing Between Stores and Bu Yearly',
    },

]);

const datatype = ref<SelectProps['options']>([
    {
        value: 'vgc',
        label: 'Verified Gc',
    },
]);

const generate = async () => {
    try {
        const response = await axios.get(route('iad.excel.generate.purchased', {
            datatype: purchase.value,
            store: storeData.value,
            date: date.value,
        }), {
            
            responseType: 'blob', // Ensures the response is treated as a Blob
        });

        // Create a URL for the Blob and trigger a download
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;

        // Optionally set a filename
        link.setAttribute('download', 'verified_report.xlsx');
        document.body.appendChild(link);
        link.click();

        // Clean up
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error generating the file:', error);
    }
};


</script>
