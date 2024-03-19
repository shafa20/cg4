<?php 
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\Controller;
class UserCrud extends Controller
{
    // show users list
    public function index(){
        $userModel = new UserModel();
        $data['users'] = $userModel->orderBy('id', 'DESC')->findAll();
        return view('user_view', $data);
    }
    // add user form
    public function create(){
        return view('add_user');
    }
 
    // insert data
    // public function store2() {
    //     $userModel = new UserModel();
    //     $data = [
    //         'name' => $this->request->getVar('name'),
    //         'email'  => $this->request->getVar('email'),
    //     ];
    //     $userModel->insert($data);
    //     return $this->response->redirect(site_url('/users-list'));
    // }
    public function store()
    {
        // Load the UserModel
        $userModel = new UserModel();
    
        // Retrieve input data
        $name = $this->request->getVar('name');
        $email = $this->request->getVar('email');
        $profilePic = $this->request->getFile('profile_pic');
    
        // Validate input data
        $validationRules = [
            'name' => 'required',
            'email' => 'required|valid_email',
            'profile_pic' => 'uploaded[profile_pic]|max_size[profile_pic,1024]|is_image[profile_pic]',
        ];
    
        if (!$this->validate($validationRules)) {
            // If validation fails, redirect back with validation errors
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    
        // Handle file upload
        if ($profilePic->isValid() && !$profilePic->hasMoved()) {
            $newName = $profilePic->getRandomName();
            $profilePic->move(ROOTPATH . 'public/uploads', $newName);
            $imageName = $profilePic->getName();
        } else {
            // If file upload fails, redirect back with error message
            return redirect()->back()->withInput()->with('error', 'Failed to upload profile picture.');
        }
    
        // Insert data into the database
        $data = [
            'name' => $name,
            'email' => $email,
            'profile_pic' => $imageName // Save the image name to the database
        ];
    
        $userModel->insert($data);
    
        // Redirect to the user list page
        return redirect()->to(site_url('/users-list'));
    }
    
    // show single user
    public function singleUser($id = null){
        
        $userModel = new UserModel();
      
        $data['user_obj'] = $userModel->where('id', $id)->first();
       
        return view('edit_user', $data);
    }
    // // update user data
    // public function update2(){
    //     $userModel = new UserModel();
    //     $id = $this->request->getVar('id');
    //     $data = [
    //         'name' => $this->request->getVar('name'),
    //         'email'  => $this->request->getVar('email'),
    //     ];
    //     $userModel->update($id, $data);
    //     return $this->response->redirect(site_url('/users-list'));
    // }
    public function update()
    {
        $userModel = new UserModel();
        $id = $this->request->getVar('id');
        $data = [
            'name' => $this->request->getVar('name'),
            'email'  => $this->request->getVar('email'),
        ];
    
        // Check if a new profile picture has been uploaded
        $profilePic = $this->request->getFile('profile_pic');
        if ($profilePic && $profilePic->isValid() && !$profilePic->hasMoved()) {
            // Handle file upload
            $newName = $profilePic->getRandomName();
            $profilePic->move(ROOTPATH . 'public/uploads', $newName);
            $imageName = $profilePic->getName();
    
            // Update the user's profile picture in the database
            $data['profile_pic'] = $imageName;
        }
    
        // Update user data
        $userModel->update($id, $data);
    
        // Redirect to the user list page
        return redirect()->to(site_url('/users-list'));
    }
    
    
    // delete user
    public function delete($id = null){
        $userModel = new UserModel();
        $data['user'] = $userModel->where('id', $id)->delete($id);
        return $this->response->redirect(site_url('/users-list'));
    }    
}