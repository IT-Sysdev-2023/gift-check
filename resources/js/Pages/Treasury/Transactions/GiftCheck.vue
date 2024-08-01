<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <a-row>
                <a-col :span="12">
                    <a-statistic
                        title="Prepaired By"
                        :value="$page.props.auth.user.full_name"
                    />
                </a-col>
                <a-col :span="12">
                    <a-statistic
                        title="Current Budget"
                        :value="remainingBudget"
                    />
                </a-col>
            </a-row>
        </a-card>
        <a-card title="Submit a Gift Check" class="mt-10">
            <a-row>
                <a-col :span="10">
                    <a-form
                        ref="formRef"
                        :model="formState"
                        :label-col="{ span: 8 }"
                        :wrapper-col="{ span: 12 }"
                    >
                        <a-form-item ref="name" label="PR No." name="name">
                            <a-input :value="prNo" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested:" name="name">
                            <a-input v-model:value="currentDate" readonly/>
                        </a-form-item>
                        <a-form-item label="Date Needed:" name="name">
                            <a-date-picker v-model:value="formState.currentDate" />
                        </a-form-item>
                        <a-form-item label="Upload Scan Copy.:" name="name">
                           <ant-upload-image/>
                        </a-form-item>
                        <a-form-item label="Remarks:." name="name">
                            <a-textarea v-model:value="formState.remarks" />
                        </a-form-item>
                    </a-form>
                </a-col>
                <a-col :span="14">
                    <a-card>
                        <a-row :gutter="16" class="text-center">
                            <a-col :span="12">
                                <span>Denomination</span>
                            </a-col>
                            <a-col :span="12">
                                <span>Quantity</span>
                            </a-col>
                        </a-row>
                        <a-row
                            :gutter="16"
                            class="mt-5"
                            v-for="(item, index) of denomination.data"
                            :key="index"
                        >
                            <a-col :span="12">
                                <a-input
                                    :value="item.denomination"
                                    readonly
                                    class="text-end"
                                />
                            </a-col>
                            <a-col :span="12">
                                <a-input
                                    v-model:value="formState.qty"
                                    type="number"
                                />
                            </a-col>
                        </a-row>
                    </a-card>

                    <a-form-item class="text-end mt-5">
                        <a-button type="primary" @click="onSubmit"
                            >Submit</a-button
                        >
                    </a-form-item>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import { Dayjs } from "dayjs";
import { reactive, ref, toRaw } from "vue";
import AuthenticatedLayout from "@/../../resources/js/Layouts/AuthenticatedLayout.vue";
import type { UnwrapRef, Ref } from "vue";
import dayjs from 'dayjs';

interface FormState {
    qty: number;
    name: string;
    remarks: string;
    currentDate: Ref<Dayjs | null>
}
defineProps<{
    title?: string;
    prNo: string;
    denomination: {
        data: any[];
    };
    remainingBudget: string;
}>();

const currentDate = dayjs().format('MMM DD, YYYY');
const formRef = ref();
const formState: UnwrapRef<FormState> = reactive({
    qty: 0,
    name: "",
    remarks: "",
    currentDate: ref<Dayjs | null>(null),
});
const onSubmit = () => {
    formRef.value
        .validate()
        .then(() => {
            console.log("values", formState, toRaw(formState));
        })
        .catch((error: any) => {
            console.log("error", error);
        });
};
const resetForm = () => {
    formRef.value.resetFields();
};
</script>
