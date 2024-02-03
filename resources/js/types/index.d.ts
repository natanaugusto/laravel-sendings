import { Config } from "ziggy-js";

export type Link = {
  url: string;
  label: string;
  active: boolean;
};

export interface Pagination<T> {
  data: T[];
  links: Link[];
}

export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: string;
}

export interface Spreadsheet {
  id: number;
  user: User;
  path: string;
  rows: number;
  imported: number;
  fails: number;
}

export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>
> = T & {
  auth: {
    user: User;
  };
  ziggy: Config & { location: string };
};

export type PagePropsTest<
  T extends Record<string, unknown> = Record<string, unknown>
> = T & {
  auth: {
    user: User;
  };
};
