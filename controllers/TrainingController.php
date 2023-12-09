<?php

class TrainingController
{
  private $twig;
  private $db;

  public function __construct($twig, $db)
  {
    $this->twig = $twig;
    $this->db = $db;
  }

  public function index()
  {
    // Obtener información del usuario actual
    $id = $_SESSION['id'];
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
    // Obtener todos los entrenamientos
    $trainings = Training::getAllTrainings($this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    $trainingDeleteMessaje = $_SESSION['training_delete_sucess'] ?? $_SESSION['training_delete_error'] ?? '';
    unset($_SESSION['training_delete_sucess'], $_SESSION['training_delete_error']);

    echo $this->twig->render('trainings.twig', [
      'full_name' => $full_name,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'avatar' => $avatar_fallback,
      'trainings' => $trainings,
      'trainingDeleteMessaje' => $trainingDeleteMessaje
    ]);
  }

  public function view($trainingId)
  {
    $id = $_SESSION['id'];
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
    // Obtener el entrenamiento por id
    $training = Training::getTrainingById($trainingId, $this->db);
    // Obtener los miembros inscritos en el entrenamiento
    $membersInClass = Training::getMembersByTrainingId($trainingId, $this->db);
    // Obtener todos los miembros
    $members = Member::getAllMembers($this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    $memberAddClassMessage = $_SESSION['member_add_class_sucess'] ?? $_SESSION['member_add_class_error'] ?? '';
    unset($_SESSION['member_add_class_sucess'], $_SESSION['member_add_class_error']);

    $memberRemoveClassMessage = $_SESSION['member_delete_class_sucess'] ?? $_SESSION['member_delete_class_error'] ?? '';
    unset($_SESSION['member_delete_class_sucess'], $_SESSION['member_delete_class_error']);

    $trainingUpdateMessage = $_SESSION['training_update_sucess'] ?? $_SESSION['training_update_error'] ?? '';
    unset($_SESSION['training_update_sucess'], $_SESSION['training_update_error']);

    echo $this->twig->render('edit-training.twig', [
      'full_name' => $full_name,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'avatar' => $avatar_fallback,
      'training' => $training,
      'members' => $membersInClass,
      'allMembers' => $members,
      'memberAddClassMessage' => $memberAddClassMessage,
      'memberRemoveClassMessage' => $memberRemoveClassMessage,
      'trainingUpdateMessage' => $trainingUpdateMessage
    ]);
  }

  public function addMemberToTraining($classId)
  {
    // Obtén los parámetros de la solicitud POST
    $memberId = $_POST['member_id'];
    // Prepara la declaración SQL
    $result = Training::addMemberToTraining($memberId, $classId, $this->db);

    if ($result) {
      $_SESSION['member_add_class_sucess'] = 'Miembro añadido a la clase correctamente';
    } else {
      $_SESSION['member_add_class_error'] = 'Error al añadrir el miembro a la clase';
    }

    // Redirige al usuario de vuelta a la página de edición de la clase
    header('Location: /dashboard/training/' . $classId);
  }

  public function deleteMemberFromClass($classId)
  {
    $memberId = $_POST['member_id'];

    $rowCount = Training::deleteMemberFromClass($memberId, $classId, $this->db);

    if ($rowCount > 0) {
      $_SESSION['member_delete_class_sucess'] = 'Miembro eliminado de la clase correctamente';
    } else {
      $_SESSION['member_delete_class_error'] = 'Error al eliminar el miembro de la clase';
    }

    // Redirige al usuario de vuelta a la página de edición de la clase
    header('Location: /dashboard/training/' . $classId);
  }

  public function create()
  {
    // Obtén los parámetros de la solicitud POST
    $class_name = $_POST['class_name'];
    $class_time = $_POST['class_time'];
    $class_days = $_POST['class_days'];
    $class_duration = $_POST['class_duration'];
    $instructor = $_POST['instructor'];
    $location = $_POST['location'];
    $poster_image = $_POST['poster_image'];

    // Llama a la función createTraining en el modelo Training
    $trainingId = Training::createTraining($class_name, $class_days, $class_time, $class_duration, $instructor, $location, $poster_image, $this->db);

    // Redirige al usuario a la página de visualización del entrenamiento recién creado
    header('Location: /dashboard/training/' . $trainingId);
  }

  public function delete($classId)
  {
    $rowCount = Training::deleteTraining($classId, $this->db);

    if ($rowCount > 0) {
      $_SESSION['training_delete_sucess'] = 'Entrenamiento eliminado correctamente';
    } else {
      $_SESSION['training_delete_error'] = 'Error al eliminar el entrenamiento';
    }

    // Redirige al usuario a la página de visualización del entrenamiento recién creado
    header('Location: /dashboard/trainings');
  }

  public function update($trainingId)
  {
    // Obtén los parámetros de la solicitud POST
    $class_name = $_POST['class_name'];
    $class_time = $_POST['class_time'];
    $class_days = $_POST['class_days'];
    $class_duration = $_POST['class_duration'];
    $instructor = $_POST['instructor'];
    $location = $_POST['location'];
    $poster_image = $_POST['poster_image'];

    // Llama a la función updateTraining en el modelo Training
    $rowCount = Training::updateTraining($trainingId, $class_name, $class_days, $class_duration, $class_time,  $instructor, $location, $poster_image, $this->db);

    if ($rowCount > 0) {
      $_SESSION['training_update_sucess'] = 'Entrenamiento actualizado correctamente';
    } else {
      $_SESSION['training_update_error'] = 'Error al actualizar el entrenamiento';
    }

    // Redirige al usuario a la página de visualización del entrenamiento actualizado
    header('Location: /dashboard/training/' . $trainingId);
  }
}
