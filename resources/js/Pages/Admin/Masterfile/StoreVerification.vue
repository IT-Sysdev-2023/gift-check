<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/core';
import { notification, Modal } from 'ant-design-vue';
import { ref, createVNode } from 'vue';
import { route } from 'ziggy-js';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';



const title = ref<string>('Store Verification');
const props = defineProps<{
    data: {
        data: object;
    },
    columns
    search: string
}>();
const search = ref<string>(props.search);
const searchFunction = () => {
    router.get(route('admin.masterfile.store.verification'), {
        search: search.value
    }, {
        preserveState: true
    });
};

const openUpdateModal = ref<boolean>(false);
const updateForm = ref(null);
const updateFund = (record) => {
    updateForm.value = record
    openUpdateModal.value = true;
};

const submitUpdate = () => {
    Modal.confirm({
        title: "Confirmation",
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode(
            "div",
            {
                style: "color:red;",
            },
            "Are you sure you want to update?",
        ),
        onOk: () => {
            router.get(route('admin.masterfile.store.verification.update'), {
                data: updateForm.value
            }, {
                onSuccess: (page) => {
                    if (page.props.flash.success) {
                        notification.success({
                            message: 'Success',
                            description: page.props.flash.success
                        });
                    }
                },
                onError: () => {
                    notification.error({
                        message: 'Error',
                        description: 'Faile to update'
                    });
                }
            });
        },
        onCancel: () => {
            console.log('Cancel');
        }
    });
};
</script>

<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item>
                <Link :href="route('admin.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                {{ title }}
            </a-breadcrumb-item>
        </a-breadcrumb>
        <a-card title="Store Verification Setup" class="mt-5">
            <div class="flex justify-end">
                <a-input-search v-model:value="search" @change="searchFunction" enter-button allow-clear
                    placeholder="Input search here..." class="w-1/4" />
            </div>
            <a-table :data-source="props.data.data" :columns="props.columns" :pagination="false" size="small"
                class="mt-5">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button title="Update User" @click="updateFund(record)" class="me-2 me-sm-5"
                            style="color: white; background-color: green">
                            <EditOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="props.data" class="mt-5" />
            <a-modal v-model:open="openUpdateModal" title="Update Store Verification" @ok="submitUpdate">
                <div class="mt-10">
                    <a-form-item label="Barcode">
                        <a-input allow-clear v-model:value="updateForm.vs_barcode" placeholder="Barcode" />
                    </a-form-item>
                    <a-form-item label="Store">
                        <a-input allow-clear v-model:value="updateForm.store" placeholder="Store" />
                    </a-form-item>
                    <a-form-item label="Vs By">
                        <a-input allow-clear v-model:value="updateForm.verifyFullname" placeholder="Vs By" />
                    </a-form-item>
                    <a-form-item label="Date">
                        <a-input allow-clear v-model:value="updateForm.vsDate" placeholder="Date" />
                    </a-form-item>
                    <a-form-item label="Time">
                        <a-input allow-clear v-model:value="updateForm.vs_time" placeholder="Time" />
                    </a-form-item>
                    <a-form-item label="Reverify Date">
                        <a-input allow-clear v-model:value="updateForm.vsReverifyDate" placeholder="Reverify Date" />
                    </a-form-item>
                    <a-form-item label="Verify By">
                        <a-input allow-clear v-model:value="updateForm.vsReverifyFullname" placeholder="Verify By" />
                    </a-form-item>
                </div>
            </a-modal>
        </a-card>
    </AuthenticatedLayout>
</template>
