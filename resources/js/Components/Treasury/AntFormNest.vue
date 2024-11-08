<template>
    <a-form
        ref="formRef"
        name="dynamic_form_nest_item"
        :model="dynamicValidateForm"
        layout="vertical"
        @finish="onFinish"
    >
        <a-space
            v-for="(user, index) in dynamicValidateForm.form"
            :key="user.primary_id"
            style="display: flex; margin-bottom: 18px"
        >
            <a-form-item label="Denomination" :name="['form', index, 'denom']">
                <a-input
                    :value="user.denomination"
                    placeholder="Denomination"
                    type="number"
                    readonly
                />
            </a-form-item>
            <a-form-item label="Quantity" :name="['form', index, 'qty']">
                <!-- {{ formCustomer?.denom[user.denomination].length }} -->
                <a-input
                    :value="user.qty"
                    placeholder="Qty"
                    type="number"
                    readonly
                />
            </a-form-item>
            <a-form-item label="Assigned">
                <a-input
                    class="w-16"
                    disabled
                    :value="formCustomer?.denom[user.denomination].length"
                    type="number"
                    readonly
                />
            </a-form-item>
            <div
                style="
                    flex-grow: 1;
                    display: flex;
                    margin-top: 40px;
                    justify-content: flex-end;
                "
            >
                <UserSwitchOutlined @click="assignEmployee(user)" />
                <MinusCircleOutlined
                    class="ml-2"
                    @click="removeUser(user)"
                    v-if="
                        props.data.some(
                            (item) => item.primary_id !== user.primary_id
                        )
                    "
                />
            </div>
        </a-space>
        <a-form-item class="pt-5">
            <a-button type="dashed" block @click="addUser">
                <PlusOutlined />
                Add Denomination
            </a-button>
        </a-form-item>
    </a-form>
    <a-modal
        v-model:open="openModalAssign"
        width="1100px"
        title="Assign Customer Employee"
        :footer="null"
    >
        <a-card>
            <ant-form-inline :data="formCustomer.denom" :denomInfo="denInfo" />
        </a-card>
    </a-modal>
</template>
<script lang="ts" setup>
import { reactive, ref } from "vue";
import { MinusCircleOutlined, PlusOutlined } from "@ant-design/icons-vue";
import AntFormInline from "./AntFormInline.vue";
import type { FormInstance } from "ant-design-vue";
import axios from "axios";

interface User {
    denomination: number;
    qty: number;
    primary_id: number;
}

const props = defineProps<{
    data: User[];
    formCustomer: any;
}>();

const denInfo = ref<User>();

const openModalAssign = ref<boolean>(false);

const formRef = ref<FormInstance>();
const dynamicValidateForm = reactive<{ form: User[] }>({
    form: [...props.data],
});
const removeUser = (item: User) => {
    const index = dynamicValidateForm.form.indexOf(item);
    if (index !== -1) {
        dynamicValidateForm.form.splice(index, 1);
    }
};
const addUser = () => {
    dynamicValidateForm.form.push({
        denomination: 0,
        qty: 0,
        primary_id: 0,
    });
};

const assignEmployee = async (denom: {
    denomination: number;
    qty: number;
    primary_id: number;
}) => {
    // const { data } = await axios.get(
    //     route("treasury.special.gc.get.assign.employee"),
    //     {
    //         params: {
    //             id: denom.primary_id,
    //         },
    //     }
    // );
    denInfo.value = denom;
    // props.formCustomer.assignCustomer = data;
    openModalAssign.value = true;
};
const onFinish = (values) => {
    console.log("Received values of form:", values);
    console.log("dynamicValidateForm.form:", dynamicValidateForm.form);
};
</script>
