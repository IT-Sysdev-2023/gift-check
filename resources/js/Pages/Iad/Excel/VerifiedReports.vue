<template>
    <AuthenticatedLayout>
        <a-card>
            <div class="flex justify-center">
                <a-select placeholder="Select Type of Report" @change="handleChangeType" ref="select"
                    style="width: 400px;" :options="selectedType">
                </a-select>
            </div>
            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <a-card class="mt-5">
                        <a-row :gutter="[16, 16]">
                            <a-col :span="12">
                                <a-date-picker class="mb-2" style="width: 100%;" v-model:value="month" picker="month" />
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
                                <a-select placeholder="Select Store" v-model:value="storeData" :disabled="vergc"
                                    class="mb-5" ref="select" style="width: 100%;" :options="selectedStores">
                                </a-select>
                            </a-col>
                        </a-row>
                    </a-card>
                </a-col>
                <a-col :span="12">
                    <a-card class="mt-5">
                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Accusantium sunt cumque iure quas at
                        fuga, ipsum cupiditate pariatur totam ratione ad, vitae laudantium! Minus eius eum amet
                        accusantium repellat nihil.
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
import { useForm } from 'laravel-precognition-vue';

const month = ref<Dayjs>();

const vergc = ref<boolean>(true);

const storeData = ref<number>();

const form = useForm('post', '', {
    datatype: '',
    store: '',
    date: '',
});


interface Records {
    stores: {
        value: number,
        label: string
    }[],
};

const props = defineProps<Records>();

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

const selectedStores = ref<SelectProps['options']>(props.stores);

const datatype = ref<SelectProps['options']>([
    {
        value: '0',
        label: 'Verified Gc',
    },
]);

const handleChangeType = (value: string) => {

}
const generate = () => {
    form.submit();
}
const handleChangeDataType = (value: string) => {
    if (value === '0') {
        vergc.value = false;
    }
}

</script>
