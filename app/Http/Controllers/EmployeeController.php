<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;


class EmployeeController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Employee::orderBy('id', 'desc')->get();
            return $this->responseSuccess($data, 'Employee List Fetch Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
        try {
            $category = Employee::create($request->all());
            return $this->responseSuccess($category, 'New Employee Created Successfully !');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $data = Employee::find($id);
            if (is_null($data)) {
                return $this->responseError(null, 'Product Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseSuccess($data, 'Product Details Fetch Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request,$id)
    {
        try {
            $employee = Employee::find($id);
            if (is_null($employee)) {
                return null;
            }
            $data = $employee->update($request->all());

            if (is_null($data))
                return $this->responseError(null, 'Employee Not Found', Response::HTTP_NOT_FOUND);

            return $this->responseSuccess($data, 'Employee Updated Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::find($id);
            if (empty($employee)) {
                return $this->responseError(null, 'Product Not Found', Response::HTTP_NOT_FOUND);
            }

            $deleted =  Employee::destroy($employee);
            if (!$deleted) {
                return $this->responseError(null, 'Failed to delete the product.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->responseSuccess($employee, 'Employee Deleted Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }


    }
}
