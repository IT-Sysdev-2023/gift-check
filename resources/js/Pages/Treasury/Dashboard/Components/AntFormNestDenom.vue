<template>
    <a-form
        ref="formRef"
        name="dynamic_form_nest_item"
        :model="form"
        layout="vertical"
    >
        <a-space
            v-for="(user, index) in form.denomination"
            :key="index"
            style="display: flex; margin-bottom: 18px"
        >
            <a-form-item label="Denomination" :name="['form', index, 'denom']">
                <ant-input-number v-model:amount="user.denomination" />
            </a-form-item>
            <a-form-item label="Quantity" :name="['form', index, 'qty']">
                <!-- {{ form?.denom[user.denomination].length }} -->
                <a-input-number v-model:value="user.qty" placeholder="Qty" />
            </a-form-item>
            <!-- <a-form-item label="Assigned">
                <a-input
                    class="w-16"
                    disabled
                    :value="form?.denom[user.denomination].length"
                    type="number"
                    readonly
                />
            </a-form-item> -->
            <div
                style="
                    flex-grow: 1;
                    display: flex;
                    margin-top: 40px;
                    justify-content: flex-end;
                "
            >
                <!-- <UserSwitchOutlined @click="assignEmployee(user)" /> -->
                <MinusCircleOutlined class="ml-2" @click="removeUser(user)" />
            </div>
        </a-space>
        <span class="text-red-500">{{ form.errors.denomination }}</span>
        
        <a-form-item class="mt-12">
            <a-button type="dashed" block @click="addUser">
                <PlusOutlined />
                Add Denomination
            </a-button>
            <a-button
                type="dashed"
                block
                danger
                @click="resetDenom"
                class="mt-5"
                v-if="form.isDirty"
            >
                <RollbackOutlined />
                Reset
            </a-button>
        </a-form-item>
    </a-form>
    <!-- <a-modal
        v-model:open="openModalAssign"
        width="1100px"
        title="Assign Customer Employee"
        :footer="null"
    >
        <a-card>
            <ant-form-inline :data="form.denom" :denomInfo="denInfo" />
        </a-card>
    </a-modal> -->
</template>
<script lang="ts" setup>
import { reactive, ref } from "vue";
import { MinusCircleOutlined, PlusOutlined } from "@ant-design/icons-vue";
import type { FormInstance } from "ant-design-vue";
import { usePage, useForm } from "@inertiajs/vue3";
interface User {
    denomination: number;
    qty: number;
    primary_id: number;
}

const props = defineProps<{
    // data: User[];
    form: any;
}>();

const denInfo = ref<User>();

const openModalAssign = ref<boolean>(false);

const formRef = ref<FormInstance>();
// const dynamicValidateForm = useForm<{ form: User[] }>({
//     form: props.form.denomination,
// });
const removeUser = (item: User) => {
    const index = props.form.denomination.indexOf(item);
    if (index !== -1) {
        props.form.denomination.splice(index, 1);
    }
};
const addUser = () => {
    props.form.denomination.push({
        id: Date.now(),
        denomination: 0,
        qty: 0,
        primary_id: Date.now(),
    });
};
const resetDenom = () => {
    props.form.reset();
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
    // props.form.assignCustomer = data;
    openModalAssign.value = true;
};
</script>
