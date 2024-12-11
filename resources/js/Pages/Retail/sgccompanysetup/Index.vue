<template>
    <AuthenticatedLayout>
        <div>
            <div class="flex justify-end mb-2">
                <a-button @click="() => addmodal = true" type="primary">
                    <PlusOutlined />Add New Company
                </a-button>
            </div>
        </div>
        <a-card title="Supplier GC Company Setup">
            <a-table size="small" bordered :pagination=false :dataSource="data.data" :columns="columns">
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'createdBy'">
                        {{ record.firstname + ' ' + record.lastname }}
                    </template>
                </template>
            </a-table>
            <Pagination :datarecords="data" class="mt-5" />
        </a-card>

        <a-modal v-model:open="addmodal" title="Add New Company" @ok="AddNewCompany">
            <a-form-item label="Company name" has-feedback :help="form.errors.companyname" :validate-status="form.errors.companyname ? 'error' : ''
                ">
                <a-input v-model:value="form.companyname" @change="form.errors.companyname =''"/>
            </a-form-item>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    data: Object
})

const addmodal = ref(false);

const form = useForm({
    companyname: ''
})


const AddNewCompany = () => {
    form.post(route('retail.sgc_company_setup.add_company'), {
        onSuccess: (e) => {
            notification[e.props.flash.type]({
                message: e.props.flash.msg,
                description: e.props.flash.description,
            });
            addmodal.value = false
        }
    })
}


const columns = [
    {
        title: 'Company Name',
        dataIndex: 'suppgc_compname'
    },
    {
        title: 'Created By',
        dataIndex: 'createdBy'
    },
    {
        title: 'Created At',
        dataIndex: 'suppgc_datecreated'
    },
]

</script>