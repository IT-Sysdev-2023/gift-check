<template>
    <a-card>
        <a-card class="mb-2" title="Promo GC Status"></a-card>
        <a-table :dataSource="dataSource" size="small" bordered :columns="columns" />
    </a-card>
   
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
export default {
    layout: AuthenticatedLayout,
    

    data() {
        return {
            dataSource: [
                {
                    key: '1',
                    company_name: 'AlladinprintPhil.com',
                    account_name: 'Red Carpet',
                    contact_person: 'Flor Javier',
                    company_number: '09175280699',
                    address: 'Pasig City'
                }
            ],

            columns: [
                {
                    title: 'Company Name',
                    dataIndex: 'company_name',
                    key: 'company_name',
                },
                {
                    title: 'Account Name',
                    dataIndex: 'account_name',
                    key: 'account_name',
                },
                {
                    title: 'Contact Person',
                    dataIndex: 'contact_person',
                    key: 'contact_person',
                },
                {
                    title: 'Company Number',
                    dataIndex: 'company_number',
                    key: 'company_number',
                },
                {
                    title: 'Address',
                    dataIndex: 'address',
                    key: 'address',
                }
            ],
        };
    },
}
</script>

