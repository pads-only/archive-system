import CustomizedTables from "./users/Users";

function App() {
  //get users data from backend using axios
  return (
    <>
      <div className="w-full flex justify-start items-center bg-amber-50 h-dvh flex-col py-12">
        <h1 className="text-5xl py-5">Users Data</h1>
        <div className="w-[80%]">
          <CustomizedTables />
        </div>
      </div>
    </>
  );
}

export default App;
