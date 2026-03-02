<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\RestaurantReport;
use Livewire\Component;

class ReportRestaurant extends Component
{
    public Restaurant $restaurant;
    public bool $showModal = false;

    public $name = '';
    public $email = '';
    public $issue_type = '';
    public $description = '';

    public function rules()
    {
        return [
            'issue_type' => 'required|string',
            'description' => 'required|string|min:10|max:1000',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ];
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'email', 'issue_type', 'description']);
        $this->resetValidation();
    }

    public function submitReport()
    {
        $this->validate();

        RestaurantReport::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->name,
            'email' => $this->email,
            'issue_type' => $this->issue_type,
            'description' => $this->description,
            'status' => 'pending',
        ]);

        session()->flash('report_success', 'Gracias por tu reporte. Lo revisaremos pronto.');

        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.report-restaurant', [
            'issueTypes' => RestaurantReport::getIssueTypes(),
        ]);
    }
}
