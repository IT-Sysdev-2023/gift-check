<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <div>
            <a-breadcrumb>
                <a-breadcrumb-item>
                    <Link :href="route('admin.dashboard')">Home</Link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ title }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </div>
        <a-card class="mt-5" title="Store Setup">
            <div class="flex justify-end">
                <a-button class="bg-blue-500 justify-end text-white" @click="() => addStore = true" type="primary">
                    <PlusOutlined /> Add New Store
                </a-button>
            </div>
            <div class="flex justify-end">
                <a-input-search v-model:value="storeSearch" @change="storeSearchFunction" allow-clear enter-button
                    placeholder="Input search here..." class="w-1/4 mt-5" />
            </div>
            <div>
                <a-table :data-source="props.data.data" :columns="props.columns" size="small" :pagination="false">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'action'">
                            <a-switch title="Issue Receipt" :checked="record.issuereceipt === 'yes'"
                                @change="(checked) => issueReceipt(record, checked)" checked-children="YES"
                                un-checked-children="NO" />
                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="props.data" class="mt-5" />
            </div>
            <a-modal v-model:open="addStore" @ok="submitStore" title="Add New Store">
                <div class="mt-5">
                    <a-form-item label="Store Name">
                        <a-input placeholder="Store Name" v-model:value="form.store_name" />
                    </a-form-item>
                    <a-form-item label="Store Code">
                        <a-input-number class="w-full" placeholder="Store Code" v-model:value="form.store_code" />
                    </a-form-item>
                    <a-form-item label="Company Code">
                        <a-input-number class="w-full" placeholder="Company Code" v-model:value="form.company_code" />
                    </a-form-item>
                    <a-form-item label="Default Password">
                        <a-input-password placeholder="Default Password" v-model:value="form.default_password" />
                    </a-form-item>
                </div>
            </a-modal>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/core';
import { notification } from 'ant-design-vue';
import { ref } from 'vue';

const title = ref<string>('Store Setup');

const props = defineProps<{
    data: {
        data: StoreData[];
    };
    columns,
    search: string
}>();

const form = ref({
    store_name: '',
    store_code: '',
    company_code: '',
    default_password: ''
});

interface StoreData {
    store_id: number;
    store_code: string;
    company_code: number;
    default_password: string;
    issuereceipt: string;
};

const issueReceipt = (record: StoreData, checked: boolean) => {
    const newValue = checked ? 'yes' : 'no';

    router.get(route('admin.masterfile.issueReceipt'), {
        id: record.store_id,
        issuereceipt: newValue
    }, {
        onSuccess: (page) => {
            if (page.props.flash.success) {
                notification.success({
                    message: 'Success',
                    description: page.props.flash.success
                });

                record.issuereceipt = newValue;
            }
        },
        onError: () => {
            notification.error({
                message: 'Error',
                description: 'Failed to update issue receipt'
            });
        }
    });
};

const storeSearch = ref<string>(props.search);

const storeSearchFunction = () => {
    router.get(route('admin.masterfile.setupStore'), {
        search: storeSearch.value
    }, {
        preserveState: true
    });
}

const addStore = ref<boolean>(false);
const submitStore = async () => {
    if (!form.value.store_name || !form.value.store_code || !form.value.company_code || !form.value.default_password) {
        notification.error({
            message: 'Error',
            description: 'Please fill all required fields'
        });
        return;
    }
    try {
        router.get(route('admin.masterfile.saveStore'), {
            store_name: form.value.store_name,
            store_code: form.value.store_code,
            company_code: form.value.company_code,
            default_password: form.value.default_password
        }, {
            onSuccess: (page) => {
                if (page.props.flash.success) {
                    notification.success({
                        message: 'Success',
                        description: page.props.flash.success
                    });

                    addStore.value = false;
                } else if (page.props.flash.error) {
                    notification.error({
                        message: 'Error',
                        description: page.props.flash.error
                    });
                    addStore.value = true;
                }
            },
            onError: () => {
                notification.error({
                    message: 'Error',
                    description: 'Failed to add store'
                });
            }
        });
    } catch (error) {
        console.error(error);
    }
}

</script>
