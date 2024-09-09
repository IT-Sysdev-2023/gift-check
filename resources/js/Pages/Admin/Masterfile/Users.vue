<template>
    <div>
        <div>
            <div class="flex justify-end mb-1">
                <a-button type="primary"><PlusOutlined />Add New User</a-button>
            </div>
        </div>
        <a-card title="Users Set Up">
            <div>
                <div class="flex justify-end">
                    <a-input-search  style="width: 300px" placeholder="Search User" enter-button
                        @search="onSearch" />
                </div>
            </div>
            <a-table :dataSource="users.data" :columns="columns" :pagination="false">
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-switch title="Set Status" @change="changeStatus(record.user_id)"
                            v-model:checked="record.status" />
                        <a-button title="Reset Password" type="primary" size="small" class="mx-2">
                            <RetweetOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
        <pagination :datarecords="users" class="mt-5" />
    </div>
</template>

<script>
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { notification } from 'ant-design-vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        users: Object
    },
    data() {
        return {
            columns: [
                {
                    title: 'Username',
                    dataIndex: 'username',
                },
                {
                    title: 'Employee ID',
                    dataIndex: 'emp_id',
                },
                {
                    title: 'First Name',
                    dataIndex: 'firstname',
                },
                {
                    title: 'Last Name',
                    dataIndex: 'lastname',
                },
                {
                    title: 'User Group',
                    dataIndex: 'title',
                },
                {
                    title: 'Store Assigned',
                    dataIndex: 'store_name',
                },
                {
                    title: 'Status',
                    dataIndex: 'user_status',
                },
                {
                    title: 'Date User Added',
                    dataIndex: 'date_created',
                },
                {
                    title: 'Action',
                    dataIndex: 'action',
                },
            ],
        }
    },
    methods: {
        changeStatus(id) {
            this.$inertia.get(route('admin.masterfile.updatestatus'), {
                id: id
            }, {
                onSuccess: (response) => {
                    notification[response.props.flash.type]({
                        message: response.props.flash.msg,
                        description: response.props.flash.description,
                    });
                }
            })
        },
        onSearch(){
            
        }
    }
}
</script>