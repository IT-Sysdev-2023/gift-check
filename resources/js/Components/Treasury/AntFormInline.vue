<template>
    <a-form
        layout="inline"
        :model="formState"
        style="margin-top: 10px"
        @finish="handleFinish"
        @finishFailed="handleFinishFailed"
    >
        <a-form-item>
            <a-input v-model:value="formState.spexgcemp_fname" placeholder="Firstname">
                <template #prefix
                    ><UserOutlined style="color: rgba(0, 0, 0, 0.25)"
                /></template>
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-input v-model:value="formState.spexgcemp_lname" placeholder="Lastname">
                <template #prefix
                    ><UserOutlined style="color: rgba(0, 0, 0, 0.25)"
                /></template>
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-input v-model:value="formState.spexgcemp_mname" placeholder="Middle Name">
                <template #prefix
                    ><UserOutlined style="color: rgba(0, 0, 0, 0.25)"
                /></template>
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-input v-model:value="formState.spexgcemp_extname" placeholder="Name Ext.">
                <template #prefix
                    ><UserOutlined style="color: rgba(0, 0, 0, 0.25)"
                /></template>
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-button
                type="primary"
                html-type="submit"
                :disabled="formState.spexgcemp_fname === '' || formState.spexgcemp_lname === ''"
            >
                Submit
            </a-button>
        </a-form-item>
    </a-form>
    <a-table
        class="mt-10"
        :columns="columns"
        :data-source="data[denomInfo.denomination]"
        bordered
    />
</template>
<script lang="ts" setup>
import { reactive, computed } from "vue";
import type { UnwrapRef } from "vue";
import type { FormProps } from "ant-design-vue";
import { notification } from "ant-design-vue";
import axios from "axios";

interface FormState {
    spexgcemp_fname: string;
    spexgcemp_lname: string;
    spexgcemp_mname: string;
    spexgcemp_extname: string;
    spexgcemp_denom: number
}


const props = defineProps<{
    data: any;
    denomInfo: {
        denomination: number;
        qty: number;
        primary_id: number;
    };
}>();

const formState: UnwrapRef<FormState> = reactive({
    spexgcemp_fname: "",
    spexgcemp_lname: "",
    spexgcemp_mname: "",
    spexgcemp_extname: "",
    spexgcemp_denom: props.denomInfo.denomination
});

const columns = [
    {
        title: "Last Name",
        dataIndex: "spexgcemp_lname",
    },
    {
        title: "First Name",
        dataIndex: "spexgcemp_fname",
    },
    {
        title: "Middle Name",
        dataIndex: "spexgcemp_mname",
    },
    {
        title: "Name Ext.",
        dataIndex: "spexgcemp_extname",
    },
];

const handleFinish: FormProps["onFinish"] = async (values) => {
    // props.denomInfo.qty
    if (props.data[props.denomInfo.denomination].length >= props.denomInfo.qty ) {
        notification.error({
            message: "Error!",
            description: "Maximum quantity has been reach!",
        });
    } else {
        props.data[props.denomInfo.denomination].push({ ...formState });
    }
};
const handleFinishFailed: FormProps["onFinishFailed"] = (errors) => {
    console.log(errors);
};
</script>

<style scoped>
.mt-10 >>> .ant-table-thead > tr > th {
    background-color: #1890ff; /* Your desired background color */
    font-weight: 500;
    color: white;
}
</style>
