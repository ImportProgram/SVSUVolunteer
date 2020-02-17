<div class="modal fade" id="modalCreateEvent" tabindex="-1" role="dialog" aria-labelledby="modalCreateEvent" aria-hidden="true">
                    <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
                      <div class="modal-content">
                        <div class="modal-body p-0">
                          <div class="card bg-secondary shadow border-0">
                            
                            <div class="card-body px-lg-5 py-lg-5">
                              <div class="text-center text-muted mb-4">
                                <h3>Create Event</h3>
                              </div>
                              <form enctype="multipart/form-data" action="" method="post">
                                <div class="form-group mb-3">
                                  <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="ionicons ion-md-add"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="Event Name" type="text" name="event_name" maxlength="50">
                                  </div>
                                </div>
                                <div class="form-group mb-3">
                                  <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="ionicons ion-md-pin"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="Event Location" type="text" name="event_location" maxlength="50">
                                  </div>
                                </div>
                                <div class="form-group mb-3">
                                  <div class="form-group">
                                    <div class="input-group input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ionicons ion-md-calendar"></i></span>
                                        </div>
                                        <input class="form-control datepicker" placeholder="Select date" type="text" name="event_date" value="<?php echo date("m/d/Y"); ?>">
                                    </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                  <div class="form-group">
                                  <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="event_icon" name="event_icon" accept="image/png">
                                    <label class="custom-file-label" for="event_icon">Event Icon (100x100)</label>
                                  </div>
                                </div>
                                </div>
                                  <div class="form-group">
                                  <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <textarea class="form-control" name="event_description" rows="3" maxlength="200" placeholder="Event Description"></textarea>
                                  </div>
                                </div>
                                <div class="text-center">
                                  <button type="submit" name="create" class="btn btn-success my-4">Create</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>