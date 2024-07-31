<template>
    <a-form
        layout="inline"
        :model="formState"
        style="text-align: center"
        @finish="handleFinish"
        @finishFailed="handleFinishFailed"
    >
        <a-form-item>
            <a-input
                v-model:value="formState.firstname"
                placeholder="Firstname"
            >
                <template #prefix
                    ><UserOutlined style="color: rgba(0, 0, 0, 0.25)"
                /></template>
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-input v-model:value="formState.lastname" placeholder="Lastname">
                <template #prefix
                    ><UserOutlined style="color: rgba(0, 0, 0, 0.25)"
                /></template>
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-input
                v-model:value="formState.middlename"
                placeholder="Middle Name"
            >
                <template #prefix
                    ><UserOutlined style="color: rgba(0, 0, 0, 0.25)"
                /></template>
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-input v-model:value="formState.nameext" placeholder="Name Ext.">
                <template #prefix
                    ><UserOutlined style="color: rgba(0, 0, 0, 0.25)"
                /></template>
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-button
                type="primary"
                html-type="submit"
                :disabled="
                    formState.firstname === '' ||
                    formState.lastname === '' ||
                    formState.middlename === '' ||
                    formState.nameext === ''
                "
            >
                Submit
            </a-button>
        </a-form-item>

    </a-form>
    <a-table class="mt-10" :columns="props.data.columns" :data-source="data.data" bordered >

    </a-table>
</template>
<script lang="ts" setup>
import { reactive } from "vue";
import { UserOutlined, LockOutlined } from "@ant-design/icons-vue";
import type { UnwrapRef } from "vue";
import type { FormProps } from "ant-design-vue";

interface FormState {
    firstname: string;
    lastname: string;
    middlename: string;
    nameext: string;
}
const formState: UnwrapRef<FormState> = reactive({
    firstname: "",
    lastname: "",
    middlename: "",
    nameext: "",
});

const props = defineProps<{
        data: any
}>();
const handleFinish: FormProps["onFinish"] = (values) => {
    console.log(values, formState);
};
const handleFinishFailed: FormProps["onFinishFailed"] = (errors) => {
    console.log(errors);
};
</script>
<style scoped>
.ant-table-thead .ant-table-cell {
  background-color: green;
}
</style>