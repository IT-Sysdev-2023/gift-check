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
                        <div v-if="isGenerating" class="mt-5">
                            <div class="flex justify-center">
                                <a-progress type="circle" :stroke-color="{
                                    '0%': '#108ee9',
                                    '100%': '#87d068',
                                }" :percent="progressBar?.percentage" />
                            </div>
                            <br>
                            <p class="text-center">{{ progressBar?.message }}</p>
                        </div>
                        <div v-else>
                            <div class="flex justify-center">
                                <img style="height:180px;" src="../../../../../public/images/excel.gif" alt="">
                            </div>
                            <br>
                            <p class="text-center">Please fill all the fields to generate!</p>
                        </div>
                    </a-card>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue';
import type { SelectProps } from 'ant-design-vue';
import type { Dayjs } from 'dayjs';
import { PageWithSharedProps } from '@/types';
import { usePage } from '@inertiajs/vue3';

const date = ref<Dayjs>();

const vergc = ref<string>('');

const selected = ref<string>('0');

const isGenerating = ref<boolean>(false);

const progressBar = ref<{
    percentage: number,
    message: string,
    currentRow: number,
    totalRows: number,
}>();

const storeData = ref<number>();


interface Records {
    stores: {
        value: number,
        label: string
    }[],
};
const page = usePage<PageWithSharedProps>().props;
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

onMounted(() => {
    window.Echo.private(`generate-verified-excel.${page.auth.user.user_id}`)
        .listen(".generate-ver-excel", (e) => {
            console.log(e);

            progressBar.value = e;
            isGenerating.value = true;
        });
})

</script>
