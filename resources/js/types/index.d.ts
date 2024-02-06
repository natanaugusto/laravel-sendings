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

export interface SpreadsheetFormData {
  file: File | null;
}

export interface Contact {
  id: number;
  name: string;
  email: string;
  phone: string;
  document: string;
  spreadsheet: Spreadsheet;
}

export interface ContactFormData {
  name: string | null;
  email: string | null;
  phone: string | null;
  document: string | null;
}

export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>
> = T & {
  auth: {
    user: User;
  };
  ziggy: Config & { location: string };
};
