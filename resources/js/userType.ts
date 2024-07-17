
import { usePage } from '@inertiajs/vue3';
import { PageProps as InertiaPageProps } from '@inertiajs/core';

export interface User {
  usertype: string,
  user_role: number,
  user_id: number,
  full_name: string,
  name: string,
  email: string,
  email_verified_at: string
}

export interface PageProps extends InertiaPageProps  {
  auth: {
    user: User;
  };
}

export function UserType() {

  const page = usePage<PageProps>();
  const user = page.props.auth.user.usertype;
  const role = page.props.auth.user.user_role;

  const userType = (userType: string) => {
    return user === userType;
  }

  const userRole = (userType: number) => {
    return role === userType;
  }



  return { userType, userRole };
}
