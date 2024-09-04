<template>
    <AuthenticatedLayout>
        <a-card title="Update Promo GC Request">
            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <a-card>
                        <a-form-item label="RFPROM No">
                            <a-input v-model:value="form.reqnum" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested">
                            <a-input v-model:value="form.dateReq" readonly />
                        </a-form-item>
                        <a-form-item label="Date Needed">
                            <a-date-picker style="width: 100%;" 
                                @change="handlechangeDate" />
                        </a-form-item>
                    </a-card>
                </a-col>
                <a-col :span="12">
                    <a-card>
                        {{ props.data[0] }}
                    </a-card>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import dayjs from 'dayjs';
import { ref } from 'vue';

const props = defineProps({ data: Object });

const form = ref({
    reqnum: props.data[0].pgcreq_id,
    dateReq: props.data[0].dateRequested,
    dateNeed: null,
})
const handlechangeDate = (obj, str) => {
    alert(str)
    form.dateNeed.value = str;
}

</script>
