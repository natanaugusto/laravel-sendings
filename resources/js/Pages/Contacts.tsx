import Pagination from "@/Components/Pagination";
import Authenticated from "@/Layouts/AuthenticatedLayout";
import { Contact, PageProps, Pagination as PaginationType } from "@/types";
import { Head, usePage } from "@inertiajs/react";

export default function Index({ auth }: PageProps) {
  const contacts = usePage().props.contacts as PaginationType<Contact>;
  return (
    <Authenticated
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          Contacts
        </h2>
      }
    >
      <Head title="Contacts" />
      <div className="sm:py-4 sm:p-4 lg:py-8 lg:p-8 ">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div className="p-6 bg-white border-b border-gray-200">
              <table className="table-fixed w-full">
                <thead>
                  <tr className="bg-gray-100">
                    <th className="px-4 py-2 w-20">ID</th>
                    <th className="px-4 py-2">Name</th>
                    <th className="px-4 py-2">Email</th>
                    <th className="px-4 py-2">Phone</th>
                    <th className="px-4 py-2">Document</th>
                    <th className="px-4 py-2">Spreadsheet</th>
                  </tr>
                </thead>
                <tbody>
                  {contacts.data.map(
                    ({ id, name, email, phone, document, spreadsheet }) => (
                      <tr key={id}>
                        <td className="border px-4 py-2">{id}</td>
                        <td className="border px-4 py-2">{name}</td>
                        <td className="border px-4 py-2">{email}</td>
                        <td className="border px-4 py-2">{phone}</td>
                        <td className="border px-4 py-2">{document}</td>
                        <td className="border px-4 py-2">{spreadsheet.path}</td>
                      </tr>
                    )
                  )}
                </tbody>
              </table>
              <Pagination links={contacts.links} />
            </div>
          </div>
        </div>
      </div>
    </Authenticated>
  );
}
