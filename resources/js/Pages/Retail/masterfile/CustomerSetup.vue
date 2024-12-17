<template>
    <AuthenticatedLayout>
        <a-card title="Manage Customer">
            <a-row>
                <a-col :span="12">
                    <a-button class="mx-2" type="primary" @click="() => createmodal = true">
                        <PlusSquareOutlined />Add New Customer
                    </a-button>
                </a-col>
                <a-col :span="12">
                    <div class="flex justify-end mb-5">
                        <a-input-search v-model:value="search" style="width: 300px;" placeholder="search..."
                            enter-button @change="onSearch" />
                    </div>
                </a-col>
            </a-row>
            <a-table :pagination="false" size="small" bordered :dataSource="data.data" :columns="columns" />
            <Pagination :datarecords="data" class="mt-3" />
        </a-card>
        <a-modal v-model:open="createmodal" style="top: 60px;" width="50%" title="Add New Customer" @ok="handleOk">
            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <a-form-item label="Firstname" has-feedback :help="form.errors.cusfname" :validate-status="form.errors.cusfname ? 'error' : ''
                        ">
                        <a-input v-model:value="form.cusfname" @change="form.errors.cusfname = ''" />
                    </a-form-item>
                    <a-form-item label="Lastname" has-feedback :help="form.errors.cuslname" :validate-status="form.errors.cuslname ? 'error' : ''
                        ">
                        <a-input v-model:value="form.cuslname" @change="form.errors.cuslname = ''" />
                    </a-form-item>
                    <a-form-item label="Middle Name">
                        <a-input v-model:value="form.cusmname" />
                    </a-form-item>
                    <a-form-item label="Name Ext.">
                        <a-input v-model:value="form.extname" />
                    </a-form-item>
                    <a-form-item label="Date of Birth" has-feedback :help="form.errors.bday" :validate-status="form.errors.bday ? 'error' : ''
                        ">
                        <a-date-picker @change="getbday" style="width: 100%;" />
                    </a-form-item>
                    <a-form-item label="Sex">
                        <a-select ref="select" v-model:value="form.sex" style="width: 100%;" placeholder="Select">
                            <a-select-option value="1">Male</a-select-option>
                            <a-select-option value="2">Female</a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
                <a-col :span="12">
                    <a-form-item label="Civil Status">
                        <a-select ref="select" v-model:value="form.cstatus" style="width: 100%;" placeholder="Select">
                            <a-select-option value="1">Single</a-select-option>
                            <a-select-option value="2">Married</a-select-option>
                            <a-select-option value="3">Widow/er</a-select-option>
                            <a-select-option value="4">Annuled</a-select-option>
                            <a-select-option value="5">Legally Separated</a-select-option>
                        </a-select>
                    </a-form-item>
                    <a-form-item label="Valid ID Number">
                        <a-input v-model:value="form.validId" />
                    </a-form-item>
                    <a-form-item label="Address" has-feedback :help="form.errors.address" :validate-status="form.errors.address ? 'error' : ''
                        ">
                        <a-input v-model:value="form.address" @change="form.errors.address = ''" />
                    </a-form-item>
                    <a-form-item label="Mobile Number">
                        <a-input type="number" v-model:value="form.mnumber" />
                    </a-form-item>
                </a-col>
            </a-row>
            <template #footer>
                <a-button key="back"  @click="() => createmodal = false">Return</a-button>
                <a-button key="submit" type="primary" :loading="loading" @click="handleOk">Submit</a-button>
            </template>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    data: Object
});

const search = ref(null);
const createmodal = ref(false);
const loading = ref(false);
const form = useForm({
    cusfname: '',
    cuslname: '',
    cusmname: '',
    extname: '',
    bday: '',
    validId: '',
    address: '',
    mnumber: '',
    sex: null,
    cstatus: null,
});
const getbday = (str, obj) => {
    form.bday = obj
};

const onSearch = () => {
    router.get(route('retail.masterfile.customer_setup'), {
        search: search.value
    }, {
        preserveState: true
    })
}

const handleOk = () => {
    form.post(route('retail.masterfile.add_customer'), {
        onStart: () => {
            loading.value = true
        },
        onSuccess: (r) => {
            loading.value = false
            notification[r.props.flash.type]({
                message: r.props.flash.msg,
                description: r.props.flash.description,
            });
        },
        onError: () => {
            loading.value = false
        },
    })
}


const columns = [
    {
        title: 'First Name',
        dataIndex: 'cus_fname',
    },
    {
        title: 'Last Name',
        dataIndex: 'cus_lname',
    },
    {
        title: 'Middle Name',
        dataIndex: 'cus_lname',
    },
    {
        title: 'Valid ID Number',
        dataIndex: 'cus_idnumber',
    },
    {
        title: 'Address',
        dataIndex: 'cus_address',
    },
    {
        title: 'Mobile Number',
        dataIndex: 'cus_mobile',
    },
]
</script>