<!-- <template>
    <a-select
      v-model:value="value"
      show-search
      placeholder="Select a person"
      style="width: 200px"
      :options="options"
      :filter-option="filterOption"
      @search="handleSearch"
      @change="handleChange"
    ></a-select>
  </template>
  <script lang="ts" setup>
  import type { SelectProps } from 'ant-design-vue';
  import { ref, watch } from 'vue';
  import axios from 'axios';
  const options = ref<SelectProps['options']>([
    { value: 'jack', label: 'Jack' },
    { value: 'lucy', label: 'Lucy' },
    { value: 'tom', label: 'Tom' },
  ]);
  const handleChange = (value: string) => {
    console.log(`selected ${value}`);
  };
  const handleSearch = async(text) => {
    const data = await axios.get(route('get.employee'), {params: {q: text}});
    console.log(data);
  };
  const filterOption = (input: string, option: any) => {
    return option.value.toLowerCase().indexOf(input.toLowerCase()) >= 0;
  };
  
  const value = ref<string | undefined>(undefined);
  </script> -->
<template>
    <a-select
        v-model:value="state.value"
        show-search
        placeholder="Select a user on Api"
        style="width: 100%"
        :filter-option="false"
        :not-found-content="state.fetching ? undefined : null"
        :options="state.data"
        @change="handleChange"
        @search="fetchUser"
    >
        <template v-if="state.fetching" #notFoundContent>
            <a-spin size="small" />
        </template>
    </a-select>
    <!-- {{ state.dataFetched }} -->
    <a-select
        v-model:value="value2"
        show-search
        placeholder="Select your user"
        style="width: 200px"
        :options="users"
        @change="handleChangeOld"
        :filter-option="filterOption2"
    ></a-select>
    <!-- {{ state.oldData }} -->
    <a-button @click="onSave" type="primary" :disabled="!value2 || !state.value"
        >Add</a-button
    >
</template>
<script setup>
import { reactive, watch, ref } from "vue";
import debounce from "lodash/debounce";
import { router, useForm } from "@inertiajs/vue3";
import { notification } from "ant-design-vue";
import axios from "axios";
let lastFetchId = 0;

const props = defineProps({
    users: [],
});
const value2 = ref(undefined);
const state = reactive({
    oldData: {},
    data: [],
    value: [],
    dataFetched: {},
    fetching: false,
});

const filterOption2 = (input, option) => {
    return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
};

const fetchUser = debounce(async (value) => {
    // console.log('fetching user', value);
    lastFetchId += 1;
    const fetchId = lastFetchId;
    state.data = [];
    state.fetching = true;

    // console.log(value);
    
    const { data } = await axios.get(route("get.employee"), {
        params: { q: value },
    });
    console.log(data.data.employee);
    const data1 = data.data.employee.map((user) => ({
        details: user,
        label: user.employee_name,
        value: user.employee_name,
    }));
    state.data = data1;
    state.fetching = false;
}, 300);
const handleChange = (value, obj) => {
    console.log(obj)
    state.dataFetched = obj.details;
};
const handleChangeOld = (value, obj) => {
    state.oldData = obj;
};
const onSave = () => {
    router.post(
        route("add.employee", state.oldData.user_id),
        { api: state.dataFetched },
        {
            onSuccess: (e) => {
                notification.success({
                    message: "Success!",
                    description: "Success you damn fck!",
                });
            },
            onError: (e) => {
                notification.error({
                    message: "Error!",
                    description: "Fill the Api user!",
                });
            },
        }
    );
    // console.log(state.oldData.user_id);
};

watch(state.value, () => {
    state.data = [];
    state.fetching = false;
});
</script>
