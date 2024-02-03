import Pagination from "@/Components/Pagination";
import Authenticated from "@/Layouts/AuthenticatedLayout";
import { PageProps, Pagination as PaginationType, Spreadsheet } from "@/types";
import { Head, usePage } from "@inertiajs/react";

export default function Index({ auth }: PageProps) {
  const spreadsheets = usePage().props.spreadsheets as PaginationType<
    Spreadsheet
  >;
  return (
    <Authenticated
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          Spreadsheets
        </h2>
      }
    >
      <Head title="Laravel 9 React JS Pagination Example with Vite - ItSolutionStuff.com" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div className="p-6 bg-white border-b border-gray-200">
              <table className="table-fixed w-full">
                <thead>
                  <tr className="bg-gray-100">
                    <th className="px-4 py-2 w-20">ID</th>
                    <th className="px-4 py-2">User</th>
                    <th className="px-4 py-2">Path</th>
                    <th className="px-4 py-2">Rows</th>
                    <th className="px-4 py-2">Imported</th>
                    <th className="px-4 py-2">Fails</th>
                  </tr>
                </thead>
                <tbody>
                  {spreadsheets.data.map(
                    ({ id, user, path, rows, imported, fails }) => (
                      <tr>
                        <td className="border px-4 py-2">{id}</td>
                        <td className="border px-4 py-2">{user.name}</td>
                        <td className="border px-4 py-2">{path}</td>
                        <td className="border px-4 py-2">{rows}</td>
                        <td className="border px-4 py-2">{imported}</td>
                        <td className="border px-4 py-2">{fails}</td>
                      </tr>
                    )
                  )}
                </tbody>
              </table>
              <Pagination links={spreadsheets.links} />
            </div>
          </div>
        </div>
      </div>
    </Authenticated>
  );
}
