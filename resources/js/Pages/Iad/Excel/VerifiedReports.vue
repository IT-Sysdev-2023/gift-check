<template>
    <AuthenticatedLayout>
        <a-card>
            <div class="flex justify-center">
                <a-select placeholder="Select Type of Report" v-model:value="selected" ref="select"
                    style="width: 400px;" :options="selectedType">
                </a-select>
            </div>
            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <a-card class="mt-5">
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
                                    :disabled="vergc !== 'vgc'" class="mb-5" ref="select" style="width: 100%;"
                                    :options="selectedStores">
                                </a-select>
                            </a-col>
                        </a-row>
                    </a-card>
                </a-col>
                <a-col :span="12">
                    <a-card class="mt-5">
                        <div class="flex justify-center">
                            <a-progress  type="circle" :stroke-color="{
                            '0%': '#108ee9',
                            '100%': '#87d068',
                        }" :percent="100" />
                        </div>
                    </a-card>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import type { SelectProps } from 'ant-design-vue';
import type { Dayjs } from 'dayjs';

const date = ref<Dayjs>();

const vergc = ref<string>('');

const selected = ref<string>('0');

const storeData = ref<number>();


interface Records {
    stores: {
        value: number,
        label: string
    }[],
};

const props = defineProps<Records>();

const selectedStores = ref<SelectProps['options']>(props.stores);

const handleDateChange = (obj: any, str: any) => {
    date.value = str;
}

const selectedType = ref<SelectProps['options']>([
    {
        value: '0',
        label: 'Verified Gc Report Monthly',
    },
    {
        value: '1',
        label: 'Verified Gc Report Yearly',
    },

]);


const datatype = ref<SelectProps['options']>([
    {
        value: 'vgc',
        label: 'Verified Gc',
    },
]);


const generate = () => {

    window.location.href = route('iad.excel.generate.verified', {
        datatype: vergc.value,
        store: storeData.value,
        date: date.value,
    });

}
const handleChangeDataType = (value: string) => {
    vergc.value = value;
}

</script>
