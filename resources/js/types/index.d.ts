export interface User {
    user_id: number;
    name: string;
    email: string;
    full_name:string;
    email_verified_at: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};

interface SharedProps extends PageProps {
    auth: {
        user: {
            id: number,
            name: string,
            email: string,
            full_name: string,
        }
    }
}

export type PageWithSharedProps = InertiaPage<SharedProps>
